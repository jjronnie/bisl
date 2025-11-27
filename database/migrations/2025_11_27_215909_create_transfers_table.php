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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            // Transfer ID from your helper function
            $table->string('transfer_id')->unique();

            // From which account field
            $table->string('from_account'); // holds the column name

            // To which account field
            $table->string('to_account');

            // Amount
            $table->decimal('amount', 20, 2);

            // Balances for audit
            $table->decimal('from_balance_before', 20, 2);
            $table->decimal('from_balance_after', 20, 2);

            $table->decimal('to_balance_before', 20, 2);
            $table->decimal('to_balance_after', 20, 2);

            // User who initiated the transfer
            $table->foreignId('transferred_by')->constrained('users')->cascadeOnDelete();

            // Purpose
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
