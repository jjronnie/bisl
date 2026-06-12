<?php

namespace App\Services;

class NssfCalculatorService
{
    const float EMPLOYEE_RATE = 0.05;

    const float EMPLOYER_RATE = 0.10;

    public function calculateEmployeeContribution(float $earnedSalary): float
    {
        return round($earnedSalary * self::EMPLOYEE_RATE, 2);
    }

    public function calculateEmployerContribution(float $earnedSalary): float
    {
        return round($earnedSalary * self::EMPLOYER_RATE, 2);
    }
}
