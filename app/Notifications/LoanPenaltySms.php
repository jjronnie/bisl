<?php

namespace App\Notifications;

use App\Models\LoanInstallment;

class LoanPenaltySms
{
    public function __construct(
        public LoanInstallment $installment,
        public string $phoneNumber,
        public string $firstName,
    ) {}

    public function getMessage(): string
    {
        $appName = strtoupper(config('services.africas_talking.sms_app_name'));
        $firstName = strtoupper($this->firstName);
        $penalty = number_format($this->installment->penalty_amount, 0);
        $loanNumber = $this->installment->loan->loan_number;

        return sprintf(
            'Dear %s, a penalty of UGX %s has been applied to loan #%s due to missed payment on %s. Please clear to avoid further charges. %s',
            $firstName,
            $penalty,
            $loanNumber,
            $this->installment->due_date->format('d/m/Y'),
            $appName
        );
    }
}
