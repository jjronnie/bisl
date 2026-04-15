<?php

namespace App\Services;

use App\Helpers\PhoneHelper;
use App\Jobs\SendSmsJob;
use App\Models\Loan;
use App\Models\SmsLog;
use App\Models\SmsSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\LoanDisbursementSms;
use App\Notifications\PaymentReceivedSms;
use App\Notifications\TransactionAlertSms;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS for loan disbursement
     * Only sent when loan is disbursed (status changed to 'active')
     */
    public static function sendLoanDisbursementSms(Loan $loan): ?SmsLog
    {
        try {
            $loan->load('member.user');
            $member = $loan->member;
            $phoneNumber = $member->phone1;

            if (! $phoneNumber) {
                Log::warning('No phone number found for member', ['member_id' => $member->id]);

                return null;
            }

            $firstName = explode(' ', $member->name)[0];
            $notification = new LoanDisbursementSms($loan, $phoneNumber, $firstName);
            $message = $notification->getMessage();

            // Create log entry
            $log = SmsLog::create([
                'phone_number' => PhoneHelper::normalize($phoneNumber),
                'message' => $message,
                'notification_type' => 'loan_disbursed',
                'recipient_id' => $member->user?->id,
                'status' => 'pending',
            ]);

            // Dispatch job
            dispatch(new SendSmsJob(
                $phoneNumber,
                $message,
                'loan_disbursed',
                (string) $member->id,
                $log->id
            ));

            return $log;
        } catch (\Exception $e) {
            Log::error('Failed to send loan disbursement SMS', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Send SMS for payment received
     */
    public static function sendPaymentReceivedSms(Loan $loan, float $amountPaid, User $user): ?SmsLog
    {
        try {
            $phoneNumber = $user->member?->phone1;
            if (! $phoneNumber) {
                Log::warning('No phone number found for user', ['user_id' => $user->id]);

                return null;
            }

            $firstName = explode(' ', $user->name)[0] ?? $user->first_name ?? 'Member';
            $notification = new PaymentReceivedSms($loan, $amountPaid, $phoneNumber, $firstName);
            $message = $notification->getMessage();

            // Create log entry
            $log = SmsLog::create([
                'phone_number' => PhoneHelper::normalize($phoneNumber),
                'message' => $message,
                'notification_type' => 'payment_received',
                'recipient_id' => $user->id,
                'status' => 'pending',
            ]);

            // Dispatch job
            dispatch(new SendSmsJob(
                $phoneNumber,
                $message,
                'payment_received',
                (string) $user->id,
                $log->id
            ));

            return $log;
        } catch (\Exception $e) {
            Log::error('Failed to send payment received SMS', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Send SMS for transaction (deposit/withdrawal/reversal)
     */
    public static function sendTransactionAlertSms(Transaction $transaction, User $user): ?SmsLog
    {
        if (! SmsSetting::isEnabled('transaction')) {
            return null;
        }

        try {
            $phoneNumber = $user->member?->phone1;
            if (! $phoneNumber) {
                Log::warning('No phone number found for user', ['user_id' => $user->id]);

                return null;
            }

            $firstName = explode(' ', $user->name)[0] ?? $user->first_name ?? 'Member';
            $notification = new TransactionAlertSms($transaction, $phoneNumber, $firstName);
            $message = $notification->getMessage();

            // Create log entry
            $log = SmsLog::create([
                'phone_number' => PhoneHelper::normalize($phoneNumber),
                'message' => $message,
                'notification_type' => 'transaction_'.$transaction->transaction_type,
                'recipient_id' => $user->id,
                'status' => 'pending',
            ]);

            // Dispatch job
            dispatch(new SendSmsJob(
                $phoneNumber,
                $message,
                'transaction_'.$transaction->transaction_type,
                (string) $user->id,
                $log->id
            ));

            return $log;
        } catch (\Exception $e) {
            Log::error('Failed to send transaction SMS', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
