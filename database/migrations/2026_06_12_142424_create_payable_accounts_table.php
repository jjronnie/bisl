<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payable_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('total_credited', 15, 2)->default(0);
            $table->decimal('total_withdrawn', 15, 2)->default(0);
            $table->timestamps();
        });

        DB::table('payable_accounts')->insert([
            ['type' => 'tax', 'balance' => 0, 'total_credited' => 0, 'total_withdrawn' => 0],
            ['type' => 'nssf', 'balance' => 0, 'total_credited' => 0, 'total_withdrawn' => 0],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payable_accounts');
    }
};
