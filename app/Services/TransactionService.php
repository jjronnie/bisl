<?php
namespace App\Services;

use App\Models\Transaction;
use App\Models\SavingsAccount;
use Illuminate\Support\Facades\DB;
use App\Mail\TransactionAlert;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;
use App\Helpers\TierHelper;
use App\Services\SmsService;

class TransactionService
{
public function create(array $data)
{
    return DB::transaction(function () use ($data) {

        $account = SavingsAccount::where('member_id', $data['member_id'])
            ->lockForUpdate()
            ->firstOrFail();

        // Decide which balance column to use
        $balanceColumn = match ($data['account']) {
            'savings' => 'balance',
            'loan_protection_fund' => 'loan_protection_fund',
            default => throw new \InvalidArgumentException('Invalid account type'),
        };

        // HARD RULE: LPF is deposit-only
        if (
            $data['account'] === 'loan_protection_fund' &&
            $data['transaction_type'] === 'withdrawal'
        ) {
            throw new \Exception('Withdrawals are not allowed from Loan Protection Fund.');
        }

        $balanceBefore = $account->{$balanceColumn};
        $amount = $data['amount'];

        // Determine debit or credit
        if ($data['transaction_type'] === 'deposit') {
            $side = 'credit';
            $balanceAfter = $balanceBefore + $amount;
        } else {
            $side = 'debit';

            if ($balanceBefore < $amount) {
                throw new \Exception('Insufficient balance.');
            }

            $balanceAfter = $balanceBefore - $amount;
        }

        // Update the correct balance
        $account->update([
            $balanceColumn => $balanceAfter,
        ]);

        // Update tier only when savings balance changes
        if ($balanceColumn === 'balance') {
            TierHelper::updateTier($account->member);
        }

        // Generate reference number
        $reference = generateTransactionId();

        // Create transaction record
        $transaction = Transaction::create([
            'member_id' => $data['member_id'],
            'reference_number' => $reference,
            'creator' => auth()->id(),

            'account' => $data['account'],
            'transaction_type' => $data['transaction_type'],
            'side' => $side,
            'amount' => $amount,

            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,

            'method' => $data['method'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);

        // Send email after commit logic
        try {
            $member = Member::with('user')->find($data['member_id']);

            if ($member?->user) {
                // Send email
                if ($member->user->email) {
                    Mail::to($member->user->email)
                        ->send(new TransactionAlert($transaction));
                }

                // Send SMS notification for deposits and withdrawals
                if (in_array($transaction->transaction_type, ['deposit', 'withdrawal'])) {
                    SmsService::sendTransactionAlertSms($transaction, $member->user);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Transaction Email/SMS Failed: ' . $e->getMessage());
        }

        return $transaction;
    });
}

}
