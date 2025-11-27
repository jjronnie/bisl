<?php
namespace App\Services;

use App\Models\Transaction;
use App\Models\SavingsAccount;
use Illuminate\Support\Facades\DB;
use App\Mail\TransactionAlert;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;
use App\Helpers\TierHelper;

class TransactionService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $account = SavingsAccount::where('member_id', $data['member_id'])->lockForUpdate()->firstOrFail();

            $balanceBefore = $account->balance;
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

            // Update the account balance
            $account->update([
                'balance' => $balanceAfter
            ]);


            // Immediately update tier
            $member = $account->member; // assuming Member has 'savingsAccount' inverse relation
            TierHelper::updateTier($member);

            // Generate reference number
            $reference = generateTransactionId();

            // Create transaction
            $transaction = Transaction::create([
                'member_id' => $data['member_id'],
                'reference_number' => $reference,
                'creator' => auth()->id(),

                'transaction_type' => $data['transaction_type'],
                'side' => $side,
                'amount' => $amount,

                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,

                'method' => $data['method'] ?? null,
                'remarks' => $data['remarks'] ?? null,

            ]);

            // 2. Send Email AFTER the transaction is fully committed
            // We wrap this in try-catch so email failure doesn't crash the HTTP response
            try {
                // Retrieve the member and their user account
                $member = Member::with('user')->find($data['member_id']);


                if ($member && $member->user && $member->user->email) {
                    Mail::to($member->user->email)->send(new TransactionAlert($transaction));
                }
            } catch (\Exception $e) {
                // Log email failure, but allow the request to succeed effectively
                // because the money has already been moved.
                \Illuminate\Support\Facades\Log::error('Transaction Email Failed: ' . $e->getMessage());
            }

            return $transaction;
        });
    }
}
