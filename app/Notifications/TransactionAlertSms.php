<?php

namespace App\Notifications;

use App\Models\Transaction;

class TransactionAlertSms
{
    public $transaction;

    public $phoneNumber;

    public $firstName;

    public function __construct(Transaction $transaction, string $phoneNumber, string $firstName)
    {
        $this->transaction = $transaction;
        $this->phoneNumber = $phoneNumber;
        $this->firstName = $firstName;
    }

    /**
     * Get SMS message for transaction alert
     * Template: Dear {NAME}, a {deposit/withdrawal} of UGX {AMOUNT} has been recorded on your {ACCOUNT_TYPE} on {DATE_TIME}. TID: {TXN_ID}. Available Bal: UGX {BALANCE}. Thank you for saving with {APP_NAME}
     */
    public function getMessage(): string
    {
        $appName = strtoupper(config('app.name'));
        $firstName = strtoupper($this->firstName);

        // Handle reversal SMS template separately
        if ($this->transaction->transaction_type === 'reversal') {
            return sprintf(
                'Dear %s, a reversal of UGX %s has been initiated on your account at %s. Available Bal: UGX %s. Please login to your account to view details. Thank you for saving with %s',
                $firstName,
                number_format($this->transaction->amount, 0),
                $this->transaction->created_at->format('d M Y H:i'),
                number_format($this->transaction->balance_after, 2),
                $appName
            );
        }

        // Get transaction type in user-friendly format
        $txnType = match ($this->transaction->transaction_type) {
            'deposit' => 'deposit',
            'withdrawal' => 'withdrawal',
            default => 'transaction',
        };

        // Get account name
        $accountName = $this->transaction->account === 'loan_protection_fund'
            ? 'Loan Protection Fund'
            : 'Savings';

        return sprintf(
            'Dear %s, a %s of UGX %s has been recorded on your %s account on %s. TID: %s. Available Bal: UGX %s. Thank you for saving with %s',
            $firstName,
            $txnType,
            number_format($this->transaction->amount, 0),
            $accountName,
            $this->transaction->created_at->format('d M Y H:i'),
            $this->transaction->reference_number,
            number_format($this->transaction->balance_after, 2),
            $appName
        );
    }
}
