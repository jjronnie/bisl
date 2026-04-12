<?php

namespace Database\Seeders;

use App\Models\SmsSetting;
use Illuminate\Database\Seeder;

class SmsSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Ensure app-wide SMS settings exist with default values
     */
    public function run(): void
    {
        // Ensure only one SMS settings row exists (app-wide)
        SmsSetting::firstOrCreate(
            [], // No conditions - just get or create first entry
            [
                'payment_notifications_enabled' => true,
                'loan_status_notifications_enabled' => true,
                'transaction_alerts_enabled' => true,
            ]
        );
    }
}
