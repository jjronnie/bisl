<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payable_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payable_account_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('reason');
            $table->foreignId('withdrawn_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('withdrawn_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payable_withdrawals');
    }
};
