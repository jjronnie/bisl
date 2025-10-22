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
      Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('savings_accounts')->onDelete('cascade');
            $table->foreignId('loan_id')->nullable()->constrained('loans')->onDelete('set null'); // Loans not yet created, will need to be run after loans table
            $table->foreignId('transacted_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->enum('transaction_type', [
                'deposit', 'withdrawal', 'loan_disbursement', 'loan_repayment', 'fee', 'other'
            ]);

            $table->decimal('amount', 12, 2);
            $table->boolean('is_debit'); // TRUE if it reduces the account balance (withdrawal, loan repayment)
            $table->decimal('running_balance', 12, 2);
            $table->string('description')->nullable();
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
