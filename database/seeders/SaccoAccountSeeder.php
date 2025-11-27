<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SaccoAccount;


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
