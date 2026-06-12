<?php

namespace App\Services;

use App\Models\PayrollAttendance;
use App\Models\PayrollMeetingAttendance;
use App\Models\PayrollProfile;
use App\Models\PayrollSetting;

class PayrollCalculatorService
{
    public function __construct(
        protected TaxCalculatorService $taxCalculator,
        protected NssfCalculatorService $nssfCalculator,
        protected AllowanceCalculatorService $allowanceCalculator,
    ) {}

    public function calculate(PayrollProfile $profile, PayrollAttendance $attendance, ?PayrollMeetingAttendance $meetingAttendance = null): array
    {
        $grade = $profile->payrollGrade;

        $dailyRate = $grade->dailyRate();
        $daysWorked = $attendance->days_worked;
        $earnedSalary = round($dailyRate * $daysWorked, 2);

        $qualificationAllowance = $this->allowanceCalculator->calculateQualificationAllowance($profile->qualification_level);
        $recognitionAllowance = $this->allowanceCalculator->calculateRecognitionAllowance($profile->recognition_level);

        $meetingCount = 0;
        $meetingAllowance = 0;

        if ($profile->meeting_allowance_eligible && $meetingAttendance) {
            $meetingCount = $meetingAttendance->meetings_attended;
            $meetingAllowance = $this->allowanceCalculator->calculateMeetingAllowance($meetingCount);
        }

        $otherAllowances = $this->allowanceCalculator->calculateOtherAllowances($profile);

        $grossSalary = round(
            $earnedSalary
            + $qualificationAllowance
            + $recognitionAllowance
            + $meetingAllowance
            + $otherAllowances,
            2
        );

        $nssfEmployee = $this->nssfCalculator->calculateEmployeeContribution($earnedSalary);

        $taxableIncome = $this->taxCalculator->calculateTaxableIncome($earnedSalary, $nssfEmployee);

        $payeResult = $this->taxCalculator->calculatePaye($taxableIncome);
        $paye = $payeResult['paye'];

        $lst = 0;

        $totalDeductions = round($paye + $nssfEmployee + $lst, 2);

        $netSalary = round($grossSalary - $totalDeductions, 2);

        $advanceAmount = (float) $attendance->advance_amount;

        $savingsContribution = round($netSalary * PayrollSetting::savingsRate(), 2);

        $finalTakeHome = round($netSalary - $savingsContribution - $advanceAmount, 2);

        return [
            'days_worked' => $daysWorked,
            'daily_rate' => round($dailyRate, 2),
            'basic_salary_earned' => $earnedSalary,
            'meeting_count' => $meetingCount,
            'meeting_allowance' => $meetingAllowance,
            'qualification_allowance' => $qualificationAllowance,
            'recognition_allowance' => $recognitionAllowance,
            'other_allowances' => $otherAllowances,
            'gross_salary' => $grossSalary,
            'nssf_employee' => $nssfEmployee,
            'taxable_income' => $taxableIncome,
            'paye' => $paye,
            'lst' => $lst,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'savings_contribution' => $savingsContribution,
            'advance_amount' => $advanceAmount,
            'final_take_home' => $finalTakeHome,
        ];
    }
}
