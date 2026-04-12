<?php

namespace App\Notifications;

use App\Models\Loan;

class LoanDisbursementSms
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
     * Get SMS message for loan disbursement
     * Template: Dear {NAME}, your Loan of UGX {AMOUNT} from {APP_NAME} has been disbursed on {DATE}, please login to your account to view details LN NUMBER: {LOAN_NUMBER}.
     */
    public function getMessage(): string
    {
        $appName = strtoupper(config('app.name'));
        $firstName = strtoupper($this->firstName);

        return sprintf(
            "Dear %s, your Loan of UGX %s from %s has been disbursed on %s, please login to your account to view details LN NUMBER: %s.",
            $firstName,
            number_format($this->loan->amount, 0),
            $appName,
            $this->loan->disbursement_date->format('d M Y'),
            $this->loan->loan_number
        );
    }
}
