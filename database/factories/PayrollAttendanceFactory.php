<?php

namespace Database\Factories;

use App\Models\PayrollAttendance;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollAttendanceFactory extends Factory
{
    protected $model = PayrollAttendance::class;

    public function definition(): array
    {
        return [
            'payroll_profile_id' => PayrollProfile::factory(),
            'payroll_period_id' => PayrollPeriod::factory(),
            'days_worked' => fake()->numberBetween(1, 31),
            'created_by' => null,
        ];
    }
}
