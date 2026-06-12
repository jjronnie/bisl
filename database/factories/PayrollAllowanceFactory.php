<?php

namespace Database\Factories;

use App\Models\AllowanceType;
use App\Models\PayrollAllowance;
use App\Models\PayrollProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollAllowanceFactory extends Factory
{
    protected $model = PayrollAllowance::class;

    public function definition(): array
    {
        return [
            'payroll_profile_id' => PayrollProfile::factory(),
            'allowance_type_id' => AllowanceType::factory(),
            'amount' => fake()->numberBetween(5000, 50000),
            'effective_from' => now()->subYear(),
            'effective_to' => null,
            'is_active' => true,
        ];
    }
}
