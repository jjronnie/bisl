<?php

namespace Database\Factories;

use App\Models\SaccoAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaccoAccountFactory extends Factory
{
    protected $model = SaccoAccount::class;

    public function definition(): array
    {
        return [
            'operational' => 0,
            'loan_interest' => 0,
            'member_interest' => 0,
            'member_savings' => 0,
            'loan_protection_fund' => 0,
        ];
    }
}
