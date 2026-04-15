<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'phone_number',
        'message',
        'notification_type',
        'recipient_id',
        'bulk_sms_campaign_id',
        'status',
        'provider_status_code',
        'provider_status_message',
        'provider_response',
        'retry_count',
        'sent_at',
        'error_message',
        'cost',
    ];

    public const PROVIDER_SUCCESS_CODES = ['100', '101', '102'];

    public const PROVIDER_FAILURE_CODES = ['103', '403', '404', '405', '500'];

    public function isProviderSuccess(): bool
    {
        return in_array($this->provider_status_code, self::PROVIDER_SUCCESS_CODES);
    }

    public function isProviderFailed(): bool
    {
        return in_array($this->provider_status_code, self::PROVIDER_FAILURE_CODES);
    }

    public function getProviderStatusLabel(): string
    {
        return match ($this->provider_status_code) {
            '100' => 'Processed/Delivered',
            '101' => 'Sent to Carrier',
            '102' => 'Queued',
            '103' => 'Rejected',
            '403' => 'Invalid Phone Number',
            '404' => 'Invalid Sender ID',
            '405' => 'Insufficient Balance',
            '500' => 'Internal Error',
            default => 'Unknown',
        };
    }

    protected $casts = [
        'sent_at' => 'datetime',
        'provider_response' => 'array',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(BulkSmsCampaign::class, 'bulk_sms_campaign_id');
    }
}
