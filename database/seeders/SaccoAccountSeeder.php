<?php

namespace Database\Seeders;

use App\Models\SaccoAccount;
use Illuminate\Database\Seeder;

class SaccoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SaccoAccount::firstOrCreate([], [
            'operational' => 0,
            'loan_interest' => 0,
            'member_interest' => 0,
            'member_savings' => 0,
            'loan_protection_fund' => 0,
        ]);
    }
}
