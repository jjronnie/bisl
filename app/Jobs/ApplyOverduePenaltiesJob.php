<?php

namespace App\Jobs;

use App\Helpers\PhoneHelper;
use App\Mail\LoanPenaltyApplied;
use App\Models\LoanInstallment;
use App\Models\SmsLog;
use App\Models\SmsSetting;
use App\Notifications\LoanPenaltySms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApplyOverduePenaltiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    public function handle(): void
    {
        Log::info('Starting overdue penalties job');

        try {
            $overdueInstallments = LoanInstallment::where('status', '!=', 'paid')
                ->where('due_date', '<', today())
                ->where('penalty_amount', 0)
                ->with('loan.member.user')
                ->get();

            if ($overdueInstallments->isEmpty()) {
                Log::info('No overdue installments found for penalty application');

                return;
            }

            $smsEnabled = SmsSetting::isEnabled('loan_status');

            foreach ($overdueInstallments as $installment) {
                try {
                    $penalty = 10000;

                    $installment->update(['penalty_amount' => $penalty]);

                    $this->sendNotifications($installment, $smsEnabled);

                    Log::info("Penalty applied to installment #{$installment->id}", [
                        'loan' => $installment->loan->loan_number,
                        'amount' => $penalty,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Failed to apply penalty for installment #{$installment->id}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Completed overdue penalties job', [
                'processed' => $overdueInstallments->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process overdue penalties', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function sendNotifications(LoanInstallment $installment, bool $smsEnabled): void
    {
        $member = $installment->loan->member;
        $user = $member->user;

        if (! $user) {
            return;
        }

        if ($user->email) {
            try {
                Mail::to($user->email)->queue(new LoanPenaltyApplied($installment));
            } catch (\Exception $e) {
                Log::warning("Failed to queue penalty email for {$user->email}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($smsEnabled && $member->phone1) {
            try {
                $firstName = explode(' ', $member->name)[0];
                $notification = new LoanPenaltySms($installment, $member->phone1, $firstName);
                $message = $notification->getMessage();

                $log = SmsLog::create([
                    'phone_number' => PhoneHelper::normalize($member->phone1),
                    'message' => $message,
                    'notification_type' => 'loan_penalty_applied',
                    'recipient_id' => $user->id,
                    'status' => 'pending',
                ]);

                dispatch(new SendSmsJob(
                    $member->phone1,
                    $message,
                    'loan_penalty_applied',
                    (string) $user->id,
                    $log->id,
                ));
            } catch (\Exception $e) {
                Log::warning("Failed to send penalty SMS for member {$member->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
