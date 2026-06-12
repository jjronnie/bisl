<?php

namespace Database\Factories;

use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use App\Models\PayrollRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollRunFactory extends Factory
{
    protected $model = PayrollRun::class;

    public function definition(): array
    {
        $dailyRate = 33333.33;
        $daysWorked = 30;
        $basicSalary = round($dailyRate * $daysWorked, 2);

        return [
            'payroll_profile_id' => PayrollProfile::factory(),
            'payroll_period_id' => PayrollPeriod::factory(),
            'days_worked' => $daysWorked,
            'daily_rate' => $dailyRate,
            'basic_salary_earned' => $basicSalary,
            'meeting_count' => 0,
            'meeting_allowance' => 0,
            'qualification_allowance' => 0,
            'recognition_allowance' => 0,
            'other_allowances' => 0,
            'gross_salary' => $basicSalary,
            'nssf_employee' => round($basicSalary * 0.05, 2),
            'taxable_income' => round($basicSalary - round($basicSalary * 0.05, 2), 2),
            'paye' => 0,
            'lst' => 0,
            'total_deductions' => round($basicSalary * 0.05, 2),
            'net_salary' => round($basicSalary - round($basicSalary * 0.05, 2), 2),
            'savings_contribution' => 0,
            'final_take_home' => 0,
            'status' => 'completed',
            'generated_at' => now(),
        ];
    }
}
