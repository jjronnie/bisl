<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\SavingsAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavingsAccountFactory extends Factory
{
    protected $model = SavingsAccount::class;

    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'account_number' => fake()->unique()->numerify('30781#####'),
            'balance' => 0,
            'loan_protection_fund' => 0,
            'membership_fee' => 0,
            'interest_earned' => 0,
            'status' => 'active',
        ];
    }
}
