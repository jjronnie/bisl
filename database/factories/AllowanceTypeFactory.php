<?php

namespace Database\Factories;

use App\Models\AllowanceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AllowanceTypeFactory extends Factory
{
    protected $model = AllowanceType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'code' => fake()->unique()->word(),
            'amount' => fake()->numberBetween(5000, 100000),
            'is_taxable' => false,
            'is_recurring' => true,
        ];
    }
}
