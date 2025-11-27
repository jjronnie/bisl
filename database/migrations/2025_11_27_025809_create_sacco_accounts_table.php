<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sacco_accounts', function (Blueprint $table) {
            $table->id();
            $table->decimal('operational', 20, 2)->default(0); // membership fees, fines, etc
            $table->decimal('loan_interest', 20, 2)->default(0); // interest collected from borrowers
            $table->decimal('loan_protection_fund', 20, 2)->default(0); // interest collected from borrowers
            $table->decimal('member_interest', 20, 2)->default(0); // liability owed to members
            $table->decimal('member_savings', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sacco_accounts');
    }
};
