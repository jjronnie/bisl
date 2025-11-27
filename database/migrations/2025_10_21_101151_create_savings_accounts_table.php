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
        Schema::create('savings_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained()->onDelete('cascade');
            $table->string('account_number')->unique();
            $table->decimal('balance', 12, 2)->default(0.00);

            $table->decimal('loan_protection_fund', 12, 2)->default(0.00);
            $table->decimal('membership_fee', 12, 2)->default(0.00);
            $table->decimal('interest_earned', 12, 2)->default(0.00);

            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_accounts');
    }
};
