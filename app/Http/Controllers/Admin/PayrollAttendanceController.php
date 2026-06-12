<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollAttendance;
use App\Models\PayrollMeetingAttendance;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use Illuminate\Http\Request;

class PayrollAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $periods = PayrollPeriod::latest('year')->latest('month')->get();

        $selectedPeriod = null;
        $profiles = collect();

        if ($request->filled('payroll_period_id')) {
            $selectedPeriod = PayrollPeriod::findOrFail($request->payroll_period_id);
            $profiles = PayrollProfile::where('is_active', true)
                ->with(['member', 'attendance' => function ($q) use ($selectedPeriod) {
                    $q->where('payroll_period_id', $selectedPeriod->id);
                }, 'meetingAttendance' => function ($q) use ($selectedPeriod) {
                    $q->where('payroll_period_id', $selectedPeriod->id);
                }])
                ->get();
        }

        return view('admin.payroll.attendance.index', compact('periods', 'selectedPeriod', 'profiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|exists:payroll_periods,id',
            'attendance' => 'required|array',
            'attendance.*.payroll_profile_id' => 'required|exists:payroll_profiles,id',
            'attendance.*.days_worked' => 'required|integer|min:0|max:31',
            'attendance.*.advance_amount' => 'nullable|numeric|min:0',
            'meeting_attendance' => 'nullable|array',
            'meeting_attendance.*.payroll_profile_id' => 'required|exists:payroll_profiles,id',
            'meeting_attendance.*.meetings_attended' => 'required|integer|min:0',
        ]);

        $periodId = $validated['payroll_period_id'];

        foreach ($validated['attendance'] as $data) {
            PayrollAttendance::updateOrCreate(
                [
                    'payroll_profile_id' => $data['payroll_profile_id'],
                    'payroll_period_id' => $periodId,
                ],
                [
                    'days_worked' => $data['days_worked'],
                    'advance_amount' => $data['advance_amount'] ?? 0,
                    'created_by' => auth()->id(),
                ]
            );
        }

        if (! empty($validated['meeting_attendance'])) {
            foreach ($validated['meeting_attendance'] as $data) {
                PayrollMeetingAttendance::updateOrCreate(
                    [
                        'payroll_profile_id' => $data['payroll_profile_id'],
                        'payroll_period_id' => $periodId,
                    ],
                    [
                        'meetings_attended' => $data['meetings_attended'],
                        'created_by' => auth()->id(),
                    ]
                );
            }
        }

        return redirect()->route('admin.payroll.periods.show', ['period' => $periodId])
            ->with('success', 'Attendance records saved successfully. You can now generate payroll.');
    }
}
