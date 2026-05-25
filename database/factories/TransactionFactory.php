<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 1000, 100000);
        $balanceBefore = fake()->randomFloat(2, 50000, 500000);

        return [
            'member_id' => Member::factory(),
            'reference_number' => 'TXN-'.fake()->unique()->numerify('########'),
            'creator' => User::factory(),
            'account' => fake()->randomElement(['savings', 'loan_protection_fund']),
            'transaction_type' => fake()->randomElement(['deposit', 'withdrawal']),
            'side' => fake()->randomElement(['credit', 'debit']),
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceBefore + $amount,
            'method' => fake()->randomElement(['cash', 'mobile_money', 'bank']),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
