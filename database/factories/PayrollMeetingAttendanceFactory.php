<?php

namespace Database\Factories;

use App\Models\PayrollMeetingAttendance;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollMeetingAttendanceFactory extends Factory
{
    protected $model = PayrollMeetingAttendance::class;

    public function definition(): array
    {
        return [
            'payroll_profile_id' => PayrollProfile::factory(),
            'payroll_period_id' => PayrollPeriod::factory(),
            'meetings_attended' => fake()->numberBetween(0, 10),
            'created_by' => null,
        ];
    }
}
