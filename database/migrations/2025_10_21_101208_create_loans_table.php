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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();


            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('loan_number')->unique();
            $table->string('loan_type'); // e.g., 'personal', 'priority', 'business'
            $table->enum('status', [
                'pending',
                'approved',
                'disbursed',
                'active',
                'completed',
                'defaulted',
                'rejected',
                'default_pending'
            ])->default('pending');

            // Financials
            $table->decimal('amount', 15, 2); // Principal
            $table->decimal('interest_rate', 5, 2); // Annual % (e.g., 18.00)
            $table->integer('duration_months');

            // Dates
            $table->date('application_date');
            $table->date('approval_date')->nullable();
            $table->date('disbursement_date')->nullable();
            $table->date('due_date')->nullable(); // Final maturity date

            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        // 2. The Amortization Schedule (Installments)
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();

            $table->integer('installment_number');
            $table->date('due_date'); // Calculated monthly date

            // Financial Breakdown (Reducing Balance)
            $table->decimal('starting_balance', 15, 2);
            $table->decimal('principal_amount', 15, 2); // The part of payment reducing the loan
            $table->decimal('interest_amount', 15, 2);  // The profit part
            $table->decimal('total_amount', 15, 2);     // Principal + Interest (EMI)
            $table->decimal('ending_balance', 15, 2);

            // Penalties
            $table->decimal('penalty_amount', 15, 2)->default(0.00);

            $table->date('paid_at')->nullable();
            $table->enum('status', ['pending', 'paid', 'partial', 'defaulted'])->default('pending');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_installments');
        Schema::dropIfExists('loans');
    }
};
