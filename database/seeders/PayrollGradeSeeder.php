<?php

namespace Database\Seeders;

use App\Models\PayrollGrade;
use Illuminate\Database\Seeder;

class PayrollGradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['name' => 'Executive Office & Administrative Support', 'monthly_basic_salary' => 900000, 'working_days_divisor' => 30],
            ['name' => 'Executive Leadership (C-Suite)', 'monthly_basic_salary' => 750000, 'working_days_divisor' => 30],
            ['name' => 'General Manager', 'monthly_basic_salary' => 600000, 'working_days_divisor' => 30],
            ['name' => 'Manager', 'monthly_basic_salary' => 450000, 'working_days_divisor' => 30],
            ['name' => 'Supervisor', 'monthly_basic_salary' => 300000, 'working_days_divisor' => 30],
            ['name' => 'Staff Member (Permanent Full-time)', 'monthly_basic_salary' => 150000, 'working_days_divisor' => 30],
            ['name' => 'Part-time Staff', 'monthly_basic_salary' => 90000, 'working_days_divisor' => 30],
            ['name' => 'Full-time Casual Staff', 'monthly_basic_salary' => 75000, 'working_days_divisor' => 30],
        ];

        foreach ($grades as $grade) {
            PayrollGrade::firstOrCreate(
                ['name' => $grade['name']],
                $grade
            );
        }
    }
}
