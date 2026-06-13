<?php

namespace Database\Seeders;

use App\Models\PayableAccount;
use Illuminate\Database\Seeder;

class PayableAccountSeeder extends Seeder
{
    public function run(): void
    {
        PayableAccount::firstOrCreate(
            ['type' => 'tax'],
            ['balance' => 0, 'total_credited' => 0, 'total_withdrawn' => 0],
        );

        PayableAccount::firstOrCreate(
            ['type' => 'nssf'],
            ['balance' => 0, 'total_credited' => 0, 'total_withdrawn' => 0],
        );
    }
}
