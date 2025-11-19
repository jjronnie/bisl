<?php
namespace App\Services;

use App\Models\Transaction;
use App\Models\SavingsAccount;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $account = SavingsAccount::where('member_id', $data['member_id'])->lockForUpdate()->firstOrFail();

            $balanceBefore = $account->balance;
            $amount = $data['amount'];

            // Determine debit or credit
            if ($data['transaction_type'] === 'deposit' ) {
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
                'description' => $data['description'] ?? null,
                'remarks' => $data['remarks'] ?? null,
               
            ]);

            return $transaction;
        });
    }
}
