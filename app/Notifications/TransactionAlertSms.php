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
     * Template: Dear {NAME}, your account has been {credited/debited} with UGX {AMOUNT} on {FULL_DATE_WITH_TIME}. TXN ID: {TXN_ID}, A/C: {ACCOUNT_NAME}, TXN TYPE: {TYPE}, Available Bal: UGX {BALANCE}. Thank you for saving with {APP_NAME}
     */
    public function getMessage(): string
    {
        $appName = strtoupper(config('app.name'));
        $firstName = strtoupper($this->firstName);
        
        // Convert side to credited/debited
        $actionText = $this->transaction->side === 'credit' ? 'credited' : 'debited';
        
        // Get transaction type in user-friendly format
        $txnType = $this->transaction->transaction_type === 'deposit' ? 'Deposit' : 'Withdrawal';
        
        // Get account name
        $accountName = $this->transaction->account === 'loan_protection_fund' 
            ? 'Loan Protection Fund' 
            : 'Savings';

        return sprintf(
            "Dear %s, your account has been %s with UGX %s on %s. TXN ID: %s, A/C: %s, TXN TYPE: %s, Available Bal: UGX %s. Thank you for saving with %s",
            $firstName,
            $actionText,
            number_format($this->transaction->amount, 0),
            $this->transaction->created_at->format('d M Y H:i A'),
            $this->transaction->reference_number,
            $accountName,
            $txnType,
            number_format($this->transaction->balance_after, 2),
            $appName
        );
    }
}
