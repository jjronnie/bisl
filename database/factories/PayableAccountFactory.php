<?php

namespace Database\Factories;

use App\Models\PayableAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayableAccountFactory extends Factory
{
    protected $model = PayableAccount::class;

    public function definition(): array
    {
        return [
            'type' => fake()->unique()->word(),
            'balance' => 0,
            'total_credited' => 0,
            'total_withdrawn' => 0,
        ];
    }

    public function tax(): static
    {
        return $this->state(fn () => ['type' => 'tax']);
    }

    public function nssf(): static
    {
        return $this->state(fn () => ['type' => 'nssf']);
    }
}
