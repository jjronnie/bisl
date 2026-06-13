<?php

namespace App\Notifications;

use App\Models\Penalty;

class PenaltyAppliedSms
{
    public function __construct(
        public Penalty $penalty,
        public string $phoneNumber,
        public string $firstName,
    ) {}

    public function getMessage(): string
    {
        $appName = strtoupper(config('services.africas_talking.sms_app_name'));
        $firstName = strtoupper($this->firstName);
        $amount = number_format($this->penalty->amount, 0);
        $reason = $this->penalty->type === 'late_meeting' ? 'Late Meeting' : 'Loss of BGG ID Card';

        return sprintf(
            'Dear %s, a penalty of UGX %s has been applied for %s. Available Bal: UGX %s. %s',
            $firstName,
            $amount,
            $reason,
            number_format($this->penalty->balance_after, 0),
            $appName
        );
    }
}
