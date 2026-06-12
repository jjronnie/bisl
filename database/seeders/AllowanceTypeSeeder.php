<?php

namespace Database\Seeders;

use App\Models\AllowanceType;
use Illuminate\Database\Seeder;

class AllowanceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $allowances = [
            ['name' => 'Certificate Qualification', 'code' => 'certificate', 'amount' => 20000, 'is_taxable' => false, 'is_recurring' => true],
            ['name' => 'Diploma Qualification', 'code' => 'diploma', 'amount' => 40000, 'is_taxable' => false, 'is_recurring' => true],
            ['name' => 'Bachelors Qualification', 'code' => 'bachelors', 'amount' => 60000, 'is_taxable' => false, 'is_recurring' => true],
            ['name' => 'Masters Qualification', 'code' => 'masters', 'amount' => 80000, 'is_taxable' => false, 'is_recurring' => true],
            ['name' => 'PhD Qualification', 'code' => 'phd', 'amount' => 100000, 'is_taxable' => false, 'is_recurring' => true],
            ['name' => 'Appreciation Certificate', 'code' => 'appreciation', 'amount' => 10000, 'is_taxable' => false, 'is_recurring' => true],
            ['name' => 'Golden Medal Award', 'code' => 'golden_medal', 'amount' => 20000, 'is_taxable' => false, 'is_recurring' => true],
            ['name' => 'Meeting Attendance', 'code' => 'meeting', 'amount' => 5000, 'is_taxable' => false, 'is_recurring' => false],
            ['name' => 'None', 'code' => 'none', 'amount' => 0, 'is_taxable' => false, 'is_recurring' => true],

        ];

        foreach ($allowances as $allowance) {
            AllowanceType::firstOrCreate(
                ['code' => $allowance['code']],
                $allowance
            );
        }
    }
}
