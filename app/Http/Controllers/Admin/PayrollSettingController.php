<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;

class PayrollSettingController extends Controller
{
    public function index()
    {
        $settings = PayrollSetting::getInstance();

        return view('admin.payroll.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'savings_deduction_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $settings = PayrollSetting::getInstance();
        $settings->update($validated);

        return redirect()->route('admin.payroll.settings.index')
            ->with('success', 'Payroll settings updated.');
    }
}
