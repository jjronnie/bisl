<?php

namespace App\Services;

use App\Helpers\TierHelper;
use App\Mail\TransactionAlert;
use App\Models\Member;
use App\Models\Reversal;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
            $amount = floatval($data['amount']);

            // Determine debit or credit
            if ($data['transaction_type'] === 'deposit') {
                $side = 'credit';
                $balanceAfter = $balanceBefore + $amount;
            } else {
                $side = 'debit';

                if ($balanceBefore < $amount) {
                    throw new \Exception('Insufficient balance. Available: UGX '.number_format($balanceBefore, 0));
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
                \Log::error('Transaction Email/SMS Failed: '.$e->getMessage());
            }

            return $transaction;
        });
    }

    public function reverse(Transaction $transaction, string $reason): Reversal
    {
        if ($transaction->transaction_type !== 'deposit') {
            throw new \Exception('Only deposits can be reversed.');
        }

        if ($transaction->created_at->lt(now()->subDays(14))) {
            throw new \Exception('Transactions older than 2 weeks cannot be reversed.');
        }

        if ($transaction->reversals()->exists()) {
            throw new \Exception('This transaction has already been reversed.');
        }

        return DB::transaction(function () use ($transaction, $reason) {
            $account = SavingsAccount::where('member_id', $transaction->member_id)
                ->lockForUpdate()
                ->firstOrFail();

            $balanceColumn = match ($transaction->account) {
                'savings' => 'balance',
                'loan_protection_fund' => 'loan_protection_fund',
                default => throw new \Exception('Invalid account type'),
            };

            $balanceBefore = $account->{$balanceColumn};
            $amount = $transaction->amount;

            if ($balanceBefore < $amount) {
                throw new \Exception('Insufficient balance to reverse transaction.');
            }

            $balanceAfter = $balanceBefore - $amount;

            $account->update([
                $balanceColumn => $balanceAfter,
            ]);

            if ($balanceColumn === 'balance') {
                TierHelper::updateTier($account->member);
            }

            $reversal = Reversal::create([
                'transaction_id' => $transaction->id,
                'reversed_by' => auth()->id(),
                'member_id' => $transaction->member_id,
                'account' => $transaction->account,
                'amount' => $amount,
                'reason' => $reason,
            ]);

            $reversalTransaction = Transaction::create([
                'member_id' => $transaction->member_id,
                'reference_number' => generateTransactionId(),
                'creator' => auth()->id(),
                'account' => $transaction->account,
                'transaction_type' => 'reversal',
                'side' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'method' => $transaction->method,
                'remarks' => "Reversal of {$transaction->reference_number}: {$reason}",
            ]);

            // Send email and SMS for reversal
            $member = Member::with('user')->find($transaction->member_id);

            if ($member?->user) {
                try {
                    if ($member->user->email) {
                        Mail::to($member->user->email)
                            ->send(new TransactionAlert($reversalTransaction));
                    }

                    SmsService::sendTransactionAlertSms($reversalTransaction, $member->user);
                } catch (\Exception $e) {
                    \Log::error('Reversal Email/SMS Failed: '.$e->getMessage());
                }
            }

            return $reversal;
        });
    }
}
