<?php

namespace App\Services;

use App\Models\PayrollRun;

class PayslipService
{
    public function generatePayslipData(PayrollRun $payrollRun): array
    {
        $payrollRun->load([
            'payrollProfile.member',
            'payrollProfile.payrollGrade',
            'payrollPeriod',
            'payrollTransactions',
        ]);

        $profile = $payrollRun->payrollProfile;
        $member = $profile->member;
        $grade = $profile->payrollGrade;
        $period = $payrollRun->payrollPeriod;

        return [
            'employee_name' => $member->name,
            'employee_number' => $profile->employee_number,
            'grade' => $grade->name,
            'period' => "{$period->month}/{$period->year}",
            'employment_type' => $profile->employment_type,
            'days_worked' => $payrollRun->days_worked,
            'monthly_basic_salary' => $grade->monthly_basic_salary,
            'daily_rate' => $payrollRun->daily_rate,
            'basic_salary_earned' => $payrollRun->basic_salary_earned,
            'qualification_allowance' => $payrollRun->qualification_allowance,
            'recognition_allowance' => $payrollRun->recognition_allowance,
            'meeting_allowance' => $payrollRun->meeting_allowance,
            'meeting_count' => $payrollRun->meeting_count,
            'other_allowances' => $payrollRun->other_allowances,
            'gross_salary' => $payrollRun->gross_salary,
            'nssf_employee' => $payrollRun->nssf_employee,
            'taxable_income' => $payrollRun->taxable_income,
            'paye' => $payrollRun->paye,
            'lst' => $payrollRun->lst,
            'total_deductions' => $payrollRun->total_deductions,
            'net_salary' => $payrollRun->net_salary,
            'savings_contribution' => $payrollRun->savings_contribution,
            'advance_amount' => $payrollRun->advance_amount,
            'final_take_home' => $payrollRun->final_take_home,
            'generated_at' => $payrollRun->generated_at,
            'transactions' => $payrollRun->payrollTransactions,
        ];
    }
}
