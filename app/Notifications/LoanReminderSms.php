<?php

namespace App\Notifications;

use App\Models\Loan;
use App\Models\LoanInstallment;

class LoanReminderSms
{
    public $loan;
    public $installment;
    public $phoneNumber;
    public $firstName;
    public $reminderType; // 'due_today' or 'due_in_7_days'

    public function __construct(Loan $loan, LoanInstallment $installment, string $phoneNumber, string $firstName, string $reminderType)
    {
        $this->loan = $loan;
        $this->installment = $installment;
        $this->phoneNumber = $phoneNumber;
        $this->firstName = $firstName;
        $this->reminderType = $reminderType;
    }

    /**
     * Get precise SMS message for loan reminder
     */
    public function getMessage(): string
    {
        $appName = strtoupper(config('app.name'));
        $firstName = strtoupper($this->firstName);
        $amount = number_format($this->installment->principal_amount + $this->installment->interest_amount + $this->installment->penalty_amount, 0);
        $loanNumber = $this->loan->loan_number;

        if ($this->reminderType === 'due_today') {
            return sprintf(
                "Dear %s, Your Loan Installment #%s is DUE TODAY. Amount: UGX %s. Please settle to avoid penalties. Thank you for saving with %s",
                $firstName,
                $loanNumber,
                $amount,
                $appName
            );
        }

        // Due in 7 days
        return sprintf(
            "Dear %s, Reminder: Your Loan Installment #%s is due in 7 days. Amount: UGX %s. Thank you for saving with %s",
            $firstName,
            $loanNumber,
            $amount,
            $appName
        );
    }
}
