<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reversals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('reversed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->enum('account', ['savings', 'loan_protection_fund']);
            $table->decimal('amount', 15, 2);
            $table->text('reason');
            $table->timestamps();

            // Index for faster lookups
            $table->index(['transaction_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reversals');
    }
};
