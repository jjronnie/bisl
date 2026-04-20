<?php

namespace App\Notifications;

use App\Models\Loan;

class PaymentReceivedSms
{
    public $loan;

    public $amountPaid;

    public $phoneNumber;

    public $firstName;

    public function __construct(Loan $loan, float $amountPaid, string $phoneNumber, string $firstName)
    {
        $this->loan = $loan;
        $this->amountPaid = $amountPaid;
        $this->phoneNumber = $phoneNumber;
        $this->firstName = $firstName;
    }

    /**
     * Get SMS message for payment received
     * If loan is settled, congratulate and thank them
     * Otherwise, standard payment received message
     */
    public function getMessage(): string
    {
        $appName = strtoupper(config('services.africas_talking.sms_app_name'));
        $firstName = strtoupper($this->firstName);

        // Check if loan is completely paid off
        if ($this->loan->status === 'completed') {
            return sprintf(
                'Dear %s, Congratulations! Your Loan #%s has been SETTLED. Thank you for choosing %s',
                $firstName,
                $this->loan->loan_number,
                $appName
            );
        }

        return sprintf(
            'Dear %s, your Loan repayment of UGX %s HAS BEEN RECIEVED. Thank you for saving with %s',
            $firstName,
            number_format($this->amountPaid, 0),
            $appName
        );
    }
}
