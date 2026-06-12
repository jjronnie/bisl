<?php

namespace Database\Factories;

use App\Models\PayrollTaxBracket;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollTaxBracketFactory extends Factory
{
    protected $model = PayrollTaxBracket::class;

    public function definition(): array
    {
        return [
            'from_amount' => fake()->numberBetween(0, 100000),
            'to_amount' => fake()->numberBetween(100001, 500000),
            'rate' => fake()->randomFloat(2, 0, 30),
            'effective_from' => '2020-01-01',
            'effective_to' => null,
        ];
    }
}
