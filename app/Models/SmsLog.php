<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'phone_number',
        'message',
        'notification_type',
        'recipient_id',
        'status',
        'provider_response',
        'retry_count',
        'sent_at',
        'error_message',
        'cost',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'provider_response' => 'array',
    ];
}
