<?php

namespace App\Notifications;

use App\Models\Loan;

class LoanStatusUpdateSms
{
    public $loan;

    public $phoneNumber;

    public $firstName;

    public function __construct(Loan $loan, string $phoneNumber, string $firstName)
    {
        $this->loan = $loan;
        $this->phoneNumber = $phoneNumber;
        $this->firstName = $firstName;
    }

    /**
     * Get brief SMS message for loan status update
     */
    public function getMessage(): string
    {
        $status = ucfirst($this->loan->status);

        $message = "Hi {$this->firstName}, your loan application #{$this->loan->loan_number} status is now: {$status}";

        if ($this->loan->status === 'approved') {
            $message .= '. Amount: UGX '.number_format($this->loan->amount, 0);
        } elseif ($this->loan->status === 'rejected') {
            $message .= '. Please contact support for details.';
        } elseif ($this->loan->status === 'disbursed') {
            $message .= '. Amount: UGX '.number_format($this->loan->amount, 0).' has been sent.';
        }

        return $message;
    }
}
