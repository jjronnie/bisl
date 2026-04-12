<?php

namespace App\Http\Controllers\Admin;

use App\Models\SmsSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmsSettingController extends Controller
{
    /**
     * Show app-wide SMS settings
     */
    public function index()
    {
        $smsSettings = SmsSetting::getInstance();

        return view('admin.sms-settings.index', compact('smsSettings'));
    }

    /**
     * Update app-wide SMS settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'payment_notifications_enabled' => 'nullable|boolean',
            'loan_status_notifications_enabled' => 'nullable|boolean',
            'transaction_alerts_enabled' => 'nullable|boolean',
        ]);

        // Convert null to false for checkboxes
        $data = [
            'payment_notifications_enabled' => $request->has('payment_notifications_enabled'),
            'loan_status_notifications_enabled' => $request->has('loan_status_notifications_enabled'),
            'transaction_alerts_enabled' => $request->has('transaction_alerts_enabled'),
        ];

        SmsSetting::getInstance()->update($data);

        return back()->with('success', 'SMS settings updated successfully.');
    }
}
