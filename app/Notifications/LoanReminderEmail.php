<?php

namespace App\Notifications;

use App\Models\Loan;
use App\Models\LoanInstallment;

class LoanReminderEmail
{
    public $loan;

    public $installment;

    public $reminderType; // 'due_today' or 'due_in_7_days'

    public function __construct(Loan $loan, LoanInstallment $installment, string $reminderType)
    {
        $this->loan = $loan;
        $this->installment = $installment;
        $this->reminderType = $reminderType;
    }

    /**
     * Get detailed email message for loan reminder
     */
    public function getSubject(): string
    {
        if ($this->reminderType === 'due_today') {
            return "Loan Installment Due Today - {$this->loan->loan_number}";
        }

        return "Loan Installment Reminder - Due in 7 Days - {$this->loan->loan_number}";
    }

    public function getBody(): array
    {
        $totalDue = $this->installment->principal_amount +
                   $this->installment->interest_amount +
                   $this->installment->penalty_amount;

        if ($this->reminderType === 'due_today') {
            return [
                'title' => 'Loan Installment Due Today',
                'message' => 'Your loan installment is due today. Please ensure you make payment to avoid penalties.',
                'dueDate' => $this->installment->due_date->format('d F Y'),
                'principal' => $this->installment->principal_amount,
                'interest' => $this->installment->interest_amount,
                'penalty' => $this->installment->penalty_amount,
                'total' => $totalDue,
            ];
        }

        // Due in 7 days
        return [
            'title' => 'Loan Installment Reminder - Due in 7 Days',
            'message' => 'Your loan installment will be due in 7 days. We recommend making payment early to avoid any issues.',
            'dueDate' => $this->installment->due_date->format('d F Y'),
            'principal' => $this->installment->principal_amount,
            'interest' => $this->installment->interest_amount,
            'penalty' => $this->installment->penalty_amount,
            'total' => $totalDue,
        ];
    }
}
