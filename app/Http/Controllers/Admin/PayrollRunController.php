<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use App\Models\PayrollRun;
use App\Services\PayslipService;
use Illuminate\Http\Request;

class PayrollRunController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollRun::with(['payrollProfile.member', 'payrollPeriod']);

        if ($request->filled('payroll_period_id')) {
            $query->where('payroll_period_id', $request->payroll_period_id);
        }

        if ($request->filled('profile_id')) {
            $query->where('payroll_profile_id', $request->profile_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $runs = $query->latest()->paginate(20)->withQueryString();

        $periods = PayrollPeriod::latest('year')->latest('month')->get();
        $profiles = PayrollProfile::with('member')->where('is_active', true)->orWhereHas('payrollRuns')->get();

        return view('admin.payroll.runs.index', compact('runs', 'periods', 'profiles'));
    }

    public function show(PayrollRun $run, PayslipService $payslipService)
    {
        $payslipData = $payslipService->generatePayslipData($run);

        return view('admin.payroll.runs.show', compact('run', 'payslipData'));
    }
}
