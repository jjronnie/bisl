<?php

namespace App\Services;

use App\Mail\SalaryDispatched;
use App\Models\PayableAccount;
use App\Models\PayrollAttendance;
use App\Models\PayrollMeetingAttendance;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use App\Models\PayrollRun;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayrollPostingService
{
    public function __construct(
        protected PayrollCalculatorService $calculator,
        protected SavingsContributionService $savingsService,
        protected TransactionService $transactionService,
    ) {}

    public function post(PayrollProfile $profile, PayrollPeriod $period, PayrollAttendance $attendance, ?PayrollMeetingAttendance $meetingAttendance = null): PayrollRun
    {
        $run = $this->generate($profile, $period, $attendance, $meetingAttendance);
        $this->dispatchRun($run);

        return $run->fresh();
    }

    public function generate(PayrollProfile $profile, PayrollPeriod $period, PayrollAttendance $attendance, ?PayrollMeetingAttendance $meetingAttendance = null): PayrollRun
    {
        $existing = PayrollRun::where('payroll_profile_id', $profile->id)
            ->where('payroll_period_id', $period->id)
            ->first();

        if ($existing && $existing->status === 'completed') {
            throw new Exception("Payroll already completed for this employee in period {$period->month}/{$period->year}.");
        }

        return DB::transaction(function () use ($profile, $period, $attendance, $meetingAttendance, $existing) {

            $calculations = $this->calculator->calculate($profile, $attendance, $meetingAttendance);

            if ($existing) {
                $existing->update(array_merge($calculations, [
                    'status' => 'draft',
                    'generated_at' => now(),
                ]));
                $payrollRun = $existing;
            } else {
                $payrollRun = PayrollRun::create(array_merge([
                    'payroll_profile_id' => $profile->id,
                    'payroll_period_id' => $period->id,
                    'status' => 'draft',
                    'generated_at' => now(),
                ], $calculations));
            }

            $payrollRun->payrollTransactions()->delete();
            $payrollRun->payrollTransactions()->createMany([
                [
                    'type' => 'salary',
                    'amount' => $payrollRun->basic_salary_earned,
                    'reference' => 'SAL-'.$payrollRun->id,
                    'description' => "Basic salary earned for {$period->month}/{$period->year}",
                ],
                [
                    'type' => 'allowance',
                    'amount' => $payrollRun->qualification_allowance + $payrollRun->recognition_allowance + $payrollRun->meeting_allowance + $payrollRun->other_allowances,
                    'reference' => 'ALW-'.$payrollRun->id,
                    'description' => "Allowances for {$period->month}/{$period->year}",
                ],
                [
                    'type' => 'paye',
                    'amount' => $payrollRun->paye,
                    'reference' => 'PAYE-'.$payrollRun->id,
                    'description' => "PAYE tax for {$period->month}/{$period->year}",
                ],
                [
                    'type' => 'nssf',
                    'amount' => $payrollRun->nssf_employee,
                    'reference' => 'NSSF-'.$payrollRun->id,
                    'description' => "NSSF employee contribution for {$period->month}/{$period->year}",
                ],
                [
                    'type' => 'lst',
                    'amount' => $payrollRun->lst,
                    'reference' => 'LST-'.$payrollRun->id,
                    'description' => "LST for {$period->month}/{$period->year}",
                ],
                [
                    'type' => 'savings',
                    'amount' => $payrollRun->savings_contribution,
                    'reference' => 'SAV-'.$payrollRun->id,
                    'description' => "Mandatory savings contribution for {$period->month}/{$period->year}",
                ],
            ]);

            return $payrollRun->fresh();
        });
    }

    public function dispatchRun(PayrollRun $payrollRun): PayrollRun
    {
        return DB::transaction(function () use ($payrollRun) {
            if ($payrollRun->status === 'completed') {
                throw new Exception("Payroll run {$payrollRun->id} is already completed.");
            }

            $period = $payrollRun->payrollPeriod;
            $profile = $payrollRun->payrollProfile;

            $payrollRun->update([
                'status' => 'completed',
                'generated_at' => now(),
            ]);

            $profile->member->salaryAccount()->firstOrCreate([
                'member_id' => $profile->member_id,
            ]);

            PayableAccount::tax()->credit((float) $payrollRun->paye);
            PayableAccount::nssf()->credit((float) $payrollRun->nssf_employee);

            $this->savingsService->depositToSavings($payrollRun, $period);

            if ($payrollRun->final_take_home > 0) {
                $this->transactionService->create([
                    'member_id' => $profile->member_id,
                    'account' => 'salary',
                    'transaction_type' => 'deposit',
                    'amount' => $payrollRun->final_take_home,
                    'method' => 'payroll',
                    'remarks' => "Salary deposit for {$period->month}/{$period->year}",
                ]);
            }

            return $payrollRun->fresh();
        });
    }

    public function dispatchPeriod(PayrollPeriod $period): void
    {
        $runs = PayrollRun::where('payroll_period_id', $period->id)
            ->where('status', 'draft')
            ->get();

        if ($runs->isEmpty()) {
            throw new Exception('No draft payroll runs to dispatch in this period.');
        }

        $errors = [];

        DB::transaction(function () use ($runs, $period, &$errors) {
            foreach ($runs as $run) {
                try {
                    $run->loadMissing('payrollProfile.member.user');
                    $this->dispatchRun($run);
                } catch (Exception $e) {
                    $errors[] = "Run {$run->id}: {$e->getMessage()}";
                }
            }

            if (! empty($errors)) {
                throw new Exception('Some runs failed to dispatch: '.implode(', ', $errors));
            }

            $period->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);
        });

        if (! empty($errors)) {
            throw new Exception('Some runs failed to dispatch: '.implode(', ', $errors));
        }

        $runs->loadMissing('payrollProfile.member.user');
        $this->sendDispatchNotifications($runs, $period);
    }

    protected function sendDispatchNotifications($runs, PayrollPeriod $period): void
    {
        foreach ($runs as $run) {
            $member = $run->payrollProfile?->member;
            if (! $member) {
                continue;
            }

            $user = $member->user;
            if (! $user || ! $user->email) {
                continue;
            }

            try {
                Mail::to($user->email)->queue(
                    new SalaryDispatched($member, $run, $period)
                );
            } catch (Exception $e) {
                Log::warning("Failed to queue salary dispatch email for {$user->email}: {$e->getMessage()}");
            }

            try {
                SmsService::sendTransactionAlertSms(
                    $member->transactions()->where('account', 'salary')->latest()->first(),
                    $user
                );
            } catch (Exception $e) {
                Log::warning("Failed to send salary dispatch SMS for member {$member->id}: {$e->getMessage()}");
            }
        }
    }
}
