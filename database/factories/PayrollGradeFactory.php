<?php

namespace Database\Factories;

use App\Models\PayrollGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollGradeFactory extends Factory
{
    protected $model = PayrollGrade::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().' Grade',
            'monthly_basic_salary' => fake()->numberBetween(500000, 5000000),
            'working_days_divisor' => 30,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
