<?php

namespace Database\Seeders;

use App\Models\PayrollTaxBracket;
use Illuminate\Database\Seeder;

class PayrollTaxBracketSeeder extends Seeder
{
    public function run(): void
    {
        $brackets = [
            ['from_amount' => 0, 'to_amount' => 235000, 'rate' => 0, 'effective_from' => '2020-01-01', 'effective_to' => null],
            ['from_amount' => 235000, 'to_amount' => 335000, 'rate' => 10, 'effective_from' => '2020-01-01', 'effective_to' => null],
            ['from_amount' => 335000, 'to_amount' => 410000, 'rate' => 20, 'effective_from' => '2020-01-01', 'effective_to' => null],
            ['from_amount' => 410000, 'to_amount' => null, 'rate' => 30, 'effective_from' => '2020-01-01', 'effective_to' => null],
        ];

        foreach ($brackets as $bracket) {
            PayrollTaxBracket::firstOrCreate(
                [
                    'from_amount' => $bracket['from_amount'],
                    'to_amount' => $bracket['to_amount'],
                    'rate' => $bracket['rate'],
                    'effective_from' => $bracket['effective_from'],
                ],
                $bracket
            );
        }
    }
}
