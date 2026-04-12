<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\LoanReminder;
use App\Notifications\LoanReminderEmail;
use App\Notifications\LoanReminderSms;
use App\Services\SmsService;
use App\Models\SmsLog;
use App\Helpers\PhoneHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendLoanRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    public function handle(): void
    {
        Log::info('Starting daily loan reminders job');

        try {
            // Get all active loans
            $activeLoans = Loan::whereIn('status', ['active', 'disbursed'])->get();

            foreach ($activeLoans as $loan) {
                $this->processLoanReminders($loan);
            }

            Log::info('Completed daily loan reminders job');
        } catch (\Exception $e) {
            Log::error('Error in SendLoanRemindersJob', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Process reminders for a single loan
     */
    private function processLoanReminders(Loan $loan): void
    {
        $loan->load('member.user', 'installments');
        $member = $loan->member;
        $user = $member->user;

        if (!$user || !$member->phone1) {
            return;
        }

        $today = today();
        $sevenDaysFromNow = today()->addDays(7);

        // Get unpaid installments
        $unpaidInstallments = $loan->installments()
            ->where('status', '!=', 'paid')
            ->get();

        foreach ($unpaidInstallments as $installment) {
            $dueDate = $installment->due_date->startOfDay();

            // Check if due today
            if ($dueDate->isSameDay($today)) {
                $this->sendReminder($loan, $installment, $user, $member, 'due_today');
            }
            // Check if due in 7 days
            elseif ($dueDate->isSameDay($sevenDaysFromNow)) {
                $this->sendReminder($loan, $installment, $user, $member, 'due_in_7_days');
            }
        }
    }

    /**
     * Send reminder via email and SMS
     */
    private function sendReminder(Loan $loan, LoanInstallment $installment, $user, $member, string $reminderType): void
    {
        $firstName = explode(' ', $member->name)[0];

        // Send Email
        if ($user->email) {
            try {
                $emailReminder = new LoanReminderEmail($loan, $installment, $reminderType);
                // You can create a Mailable class or handle it here
                Mail::send('emails.loan.reminder', [
                    'loan' => $loan,
                    'installment' => $installment,
                    'reminder' => $emailReminder,
                ], function ($message) use ($user, $emailReminder) {
                    $message->to($user->email)
                            ->subject($emailReminder->getSubject());
                });

                // Log the email reminder
                LoanReminder::create([
                    'loan_id' => $loan->id,
                    'installment_id' => $installment->id,
                    'type' => $reminderType,
                    'channel' => 'email',
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send loan reminder email', ['error' => $e->getMessage()]);
            }
        }

        // Send SMS
        if ($member->phone1) {
            try {
                $smsReminder = new LoanReminderSms($loan, $installment, $member->phone1, $firstName, $reminderType);
                $message = $smsReminder->getMessage();

                // Create SMS log
                $smsLog = SmsLog::create([
                    'phone_number' => PhoneHelper::normalize($member->phone1),
                    'message' => $message,
                    'notification_type' => 'loan_reminder_' . $reminderType,
                    'recipient_id' => $user->id,
                    'status' => 'pending',
                ]);

                // Dispatch SMS job
                dispatch(new SendSmsJob(
                    $member->phone1,
                    $message,
                    'loan_reminder_' . $reminderType,
                    (string) $member->id,
                    $smsLog->id
                ));

                // Log the reminder
                LoanReminder::create([
                    'loan_id' => $loan->id,
                    'installment_id' => $installment->id,
                    'type' => $reminderType,
                    'channel' => 'sms',
                    'status' => 'pending',
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send loan reminder SMS', ['error' => $e->getMessage()]);
            }
        }
    }
}
