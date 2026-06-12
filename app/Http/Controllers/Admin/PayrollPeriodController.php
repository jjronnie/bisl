<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchPayrollJob;
use App\Jobs\GeneratePayrollJob;
use App\Models\PayrollAttendance;
use App\Models\PayrollMeetingAttendance;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use App\Models\PayrollRun;
use App\Services\PayrollPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayrollPeriodController extends Controller
{
    public function index()
    {
        $periods = PayrollPeriod::latest('year')->latest('month')->paginate(20);

        return view('admin.payroll.periods.index', compact('periods'));
    }

    public function create()
    {
        return view('admin.payroll.periods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000|max:2099',
        ]);

        $exists = PayrollPeriod::where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['month' => 'A payroll period for this month and year already exists.']);
        }

        $period = PayrollPeriod::create($validated);

        return redirect()->route('admin.payroll.attendance.index', ['payroll_period_id' => $period->id])
            ->with('success', 'Payroll period created. Now record employee attendance.');
    }

    public function show(PayrollPeriod $period)
    {
        $period->loadMissing(['payrollRuns.payrollProfile.member']);

        $activeProfiles = PayrollProfile::where('is_active', true)
            ->with('member', 'payrollGrade', 'attendance', 'meetingAttendance')
            ->get();

        $attendanceExists = PayrollAttendance::where('payroll_period_id', $period->id)
            ->pluck('payroll_profile_id')
            ->toArray();

        $meetingAttendanceExists = PayrollMeetingAttendance::where('payroll_period_id', $period->id)
            ->pluck('payroll_profile_id')
            ->toArray();

        $hasDraftRuns = $period->payrollRuns->where('status', 'draft')->isNotEmpty();
        $allCompleted = $period->payrollRuns->isNotEmpty() && $period->payrollRuns->every(fn ($r) => $r->status === 'completed');

        $totalGross = $period->payrollRuns->sum('gross_salary');
        $totalDeductions = $period->payrollRuns->sum('total_deductions');
        $totalNet = $period->payrollRuns->sum('net_salary');
        $totalSavings = $period->payrollRuns->sum('savings_contribution');
        $totalTakeHome = $period->payrollRuns->sum('final_take_home');
        $totalPaye = $period->payrollRuns->sum('paye');

        return view('admin.payroll.periods.show', compact(
            'period',
            'activeProfiles',
            'attendanceExists',
            'meetingAttendanceExists',
            'hasDraftRuns',
            'allCompleted',
            'totalGross',
            'totalDeductions',
            'totalNet',
            'totalSavings',
            'totalTakeHome',
            'totalPaye',
        ));
    }

    public function generate(PayrollPeriod $period)
    {
        if ($period->status === 'completed') {
            return back()->with('error', 'This period is already completed.');
        }

        if ($period->status === 'cancelled') {
            return back()->with('error', 'Cancelled periods cannot be processed.');
        }

        $profileIds = PayrollProfile::where('is_active', true)->pluck('id')->toArray();

        $missingAttendance = [];
        foreach ($profileIds as $pid) {
            $exists = PayrollAttendance::where('payroll_profile_id', $pid)
                ->where('payroll_period_id', $period->id)
                ->exists();

            if (! $exists) {
                $profile = PayrollProfile::with('member')->find($pid);
                if ($profile) {
                    $missingAttendance[] = $profile->member?->name ?? "Profile #{$pid}";
                }
            }
        }

        if (! empty($missingAttendance)) {
            return back()->with('error', 'Attendance not recorded for: '.implode(', ', $missingAttendance));
        }

        $period->update(['status' => 'processing']);
        GeneratePayrollJob::dispatch($period, $profileIds);

        return redirect()->route('admin.payroll.periods.show', ['period' => $period->id])
            ->with('success', 'Payroll generation has been queued. Runs will appear as draft once processed.');
    }

    public function regenerate(PayrollPeriod $period, PayrollPostingService $postingService)
    {
        if ($period->status === 'completed') {
            return back()->with('error', 'This period is already completed and cannot be regenerated.');
        }

        $profiles = PayrollProfile::where('is_active', true)->get();
        $count = 0;

        foreach ($profiles as $profile) {
            $attendance = PayrollAttendance::where('payroll_profile_id', $profile->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            if (! $attendance) {
                continue;
            }

            $meetingAttendance = PayrollMeetingAttendance::where('payroll_profile_id', $profile->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            try {
                $postingService->generate($profile, $period, $attendance, $meetingAttendance);
                $count++;
            } catch (\Exception $e) {
                Log::error("Regenerate failed for profile {$profile->id}: {$e->getMessage()}");
            }
        }

        if ($count === 0) {
            return back()->with('error', 'No employees found to regenerate.');
        }

        return redirect()->route('admin.payroll.periods.show', ['period' => $period->id])
            ->with('success', "Payroll regenerated for {$count} employee(s).");
    }

    public function status(PayrollPeriod $period)
    {
        return response()->json([
            'status' => $period->fresh()->status,
        ]);
    }

    public function dispatch(PayrollPeriod $period)
    {
        if ($period->status === 'completed') {
            return back()->with('error', 'This period is already completed.');
        }

        $draftCount = PayrollRun::where('payroll_period_id', $period->id)
            ->where('status', 'draft')
            ->count();

        if ($draftCount === 0) {
            return back()->with('error', 'No draft payroll runs to dispatch. Generate payroll first.');
        }

        $period->update(['status' => 'processing']);
        DispatchPayrollJob::dispatch($period);

        return redirect()->route('admin.payroll.periods.show', ['period' => $period->id])
            ->with('success', 'Salary dispatch has been queued. The period will close once processing completes.');
    }
}
