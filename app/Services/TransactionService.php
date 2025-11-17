<?php

namespace App\Services;

use App\HttpModels\Member;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InsufficientFundsException; 
use App\Helpers\TransactionHelper;
use Carbon\Carbon;

class TransactionService
{
    /**
     * Creates a new transaction and updates the member's savings account balance.
     *
     * @param array $data Validated data from StoreTransactionRequest
     * @return Transaction
     * @throws InsufficientFundsException
     */
    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {

            $account = SavingsAccount::findOrFail($data['savings_account_id']);

            // Calculate new balance
            $newBalance = $data['is_debit'] 
                ? $account->balance - $data['amount']
                : $account->balance + $data['amount'];

            if ($newBalance < 0) {
                throw new \Exception('Insufficient balance for this transaction.');
            }

            // Update account balance
            $account->balance = $newBalance;
            $account->save();

            // Create transaction
            $transaction = Transaction::create([
                'member_id' => $data['member_id'],
                'savings_account_id' => $data['savings_account_id'],
                'loan_id' => $data['loan_id'] ?? null,
                'transacted_by_user_id' => $data['transacted_by_user_id'] ?? null,
                'transaction_type' => $data['transaction_type'],
                'method' => $data['method'] ?? null,
                'amount' => $data['amount'],
                'is_debit' => $data['is_debit'],
                'running_balance' => $newBalance,
                'description' => $data['description'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? Carbon::now(),
                'id' => TransactionHelper::generateUniqueTransactionId(), 
            ]);

            return $transaction;
        });
    }

    /**
     * Generates a unique 12-digit transaction ID starting with "134".
     *
     * @return string
     */
   
}