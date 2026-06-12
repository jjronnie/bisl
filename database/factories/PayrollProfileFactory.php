<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\PayrollGrade;
use App\Models\PayrollProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollProfileFactory extends Factory
{
    protected $model = PayrollProfile::class;

    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'payroll_grade_id' => PayrollGrade::factory(),
            'employee_number' => fake()->unique()->numerify('EMP-#####'),
            'employment_type' => fake()->randomElement(['permanent', 'part_time', 'casual']),
            'qualification_level' => fake()->randomElement(['certificate', 'diploma', 'bachelors', 'masters', 'phd']),
            'recognition_level' => fake()->randomElement(['none', 'appreciation', 'golden_medal']),
            'meeting_allowance_eligible' => fake()->boolean(),
            'employment_start_date' => fake()->date(),
            'employment_end_date' => null,
            'is_active' => true,
        ];
    }
}
