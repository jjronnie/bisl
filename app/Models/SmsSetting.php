<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    protected $fillable = [
        'payment_notifications_enabled',
        'loan_status_notifications_enabled',
        'transaction_alerts_enabled',
        'send_salary_sms',
    ];

    protected $casts = [
        'payment_notifications_enabled' => 'boolean',
        'loan_status_notifications_enabled' => 'boolean',
        'transaction_alerts_enabled' => 'boolean',
        'send_salary_sms' => 'boolean',
    ];

    /**
     * Get the singleton instance (app-wide settings)
     */
    public static function getInstance(): self
    {
        return self::firstOrFail();
    }

    /**
     * Check if a notification type is enabled globally
     */
    public static function isEnabled(string $type): bool
    {
        $settings = self::getInstance();

        return match ($type) {
            'payment' => $settings->payment_notifications_enabled,
            'loan_status' => $settings->loan_status_notifications_enabled,
            'transaction' => $settings->transaction_alerts_enabled,
            'salary' => $settings->send_salary_sms,
            default => false,
        };
    }
}
