<?php

namespace App\Jobs;

use App\Helpers\PhoneHelper;
use App\Mail\SalaryDispatched;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\SmsLog;
use App\Models\SmsSetting;
use App\Services\PayrollPostingService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DispatchPayrollJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 3;

    public $backoff = [60, 120, 300];

    public function __construct(
        public PayrollPeriod $period
    ) {}

    public function handle(PayrollPostingService $postingService): void
    {
        $runs = PayrollRun::where('payroll_period_id', $this->period->id)
            ->where('status', 'draft')
            ->get();

        if ($runs->isEmpty()) {
            Log::warning("DispatchPayrollJob: No draft runs for period {$this->period->id}.");

            return;
        }

        $errors = [];

        DB::transaction(function () use ($runs, &$errors, $postingService) {
            foreach ($runs as $run) {
                try {
                    $run->loadMissing('payrollProfile.member.user');
                    $postingService->dispatchRun($run);
                } catch (Exception $e) {
                    $errors[] = "Run {$run->id}: {$e->getMessage()}";
                }
            }

            if (! empty($errors)) {
                throw new Exception('Some runs failed to dispatch: '.implode(', ', $errors));
            }

            $this->period->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);
        });

        if (! empty($errors)) {
            Log::error('DispatchPayrollJob failed', ['period_id' => $this->period->id, 'errors' => $errors]);

            return;
        }

        $runs->loadMissing('payrollProfile.member.user');

        $smsEnabled = SmsSetting::isEnabled('salary');
        $emailIndex = 0;
        $smsIndex = 0;

        foreach ($runs as $run) {
            $member = $run->payrollProfile?->member;
            if (! $member) {
                continue;
            }

            $user = $member->user;
            if (! $user) {
                continue;
            }

            // Queue email with staggered delay (5 per minute = 1 per 12 seconds)
            if ($user->email) {
                try {
                    $emailDelay = now()->addSeconds($emailIndex * 12);
                    $emailIndex++;

                    Mail::to($user->email)->later(
                        $emailDelay,
                        new SalaryDispatched($member, $run, $this->period)
                    );
                } catch (Exception $e) {
                    Log::warning("Failed to queue salary dispatch email for {$user->email}: {$e->getMessage()}");
                }
            }

            // Queue SMS with staggered delay (5 per minute = 1 per 12 seconds)
            $phone = $member->phone1;
            if ($phone && $smsEnabled) {
                try {
                    $smsDelay = now()->addSeconds($smsIndex * 12);
                    $smsIndex++;

                    $periodLabel = date('F', mktime(0, 0, 0, $this->period->month, 1)).' '.$this->period->year;
                    $firstName = explode(' ', $user->name)[0] ?? 'Member';
                    $message = "Dear {$firstName}, your {$periodLabel} salary is ready. Please login to your account to see your balances.";

                    $log = SmsLog::create([
                        'phone_number' => PhoneHelper::normalize($phone),
                        'message' => $message,
                        'notification_type' => 'salary_dispatched',
                        'recipient_id' => $user->id,
                        'status' => 'pending',
                    ]);

                    dispatch(new SendSmsJob(
                        $phone,
                        $message,
                        'salary_dispatched',
                        (string) $user->id,
                        $log->id,
                    ))->delay($smsDelay);
                } catch (Exception $e) {
                    Log::warning("Failed to send salary dispatch SMS for member {$member->id}: {$e->getMessage()}");
                }
            }
        }
    }
}
