<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use Illuminate\Http\Request;

class PayrollDashboardController extends Controller
{
    public function index(Request $request)
    {
        $periods = PayrollPeriod::latest('year')->latest('month')->get();

        $selectedPeriodId = $request->input('period_id');
        $selectedPeriod = $selectedPeriodId ? PayrollPeriod::find($selectedPeriodId) : PayrollPeriod::latest('year')->latest('month')->first();

        $employees = collect();
        $periodStats = [
            'totalActiveEmployees' => 0,
            'totalGross' => 0,
            'totalSavings' => 0,
            'totalNssf' => 0,
            'totalPaye' => 0,
            'totalNet' => 0,
        ];

        if ($selectedPeriod) {
            $runs = PayrollRun::where('payroll_period_id', $selectedPeriod->id)
                ->with('payrollProfile.member', 'payrollProfile.payrollGrade')
                ->get();

            $periodStats = [
                'totalActiveEmployees' => $runs->count(),
                'totalGross' => $runs->sum('gross_salary'),
                'totalSavings' => $runs->sum('savings_contribution'),
                'totalNssf' => $runs->sum('nssf_employee'),
                'totalPaye' => $runs->sum('paye'),
                'totalNet' => $runs->sum('net_salary'),
            ];

            $employees = $runs->map(function ($run) {
                return [
                    'id' => $run->payrollProfile->id,
                    'name' => $run->payrollProfile->member?->name ?? 'N/A',
                    'employee_number' => $run->payrollProfile->employee_number,
                    'days_worked' => $run->days_worked,
                    'gross_salary' => $run->gross_salary,
                    'savings_contribution' => $run->savings_contribution,
                    'nssf_employee' => $run->nssf_employee,
                    'paye' => $run->paye,
                    'net_salary' => $run->net_salary,
                    'status' => $run->status,
                ];
            });
        }

        return view('admin.payroll.dashboard', compact(
            'periods',
            'selectedPeriod',
            'employees',
            'periodStats',
        ));
    }
}
