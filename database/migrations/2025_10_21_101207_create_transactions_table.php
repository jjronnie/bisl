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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('savings_account_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('loan_id')->nullable()->constrained('loans')->onDelete('set null');
            $table->foreignId('transacted_by_user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('transaction_type', [
                'deposit',
                'withdrawal',
                'loan_disbursement',
                'loan_repayment',
                'fee',
                'other'
            ]);
            $table->string('method')->nullable();

            $table->decimal('amount', 15, 2);
            $table->boolean('is_debit');
            $table->decimal('running_balance', 15, 2);
            $table->string('description')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('transaction_date')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
