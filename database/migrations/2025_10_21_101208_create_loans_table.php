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
Schema::create('loans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained()->onDelete('cascade');
    $table->string('loan_number')->unique();

    $table->decimal('principal_amount', 15, 2);
    $table->decimal('interest_rate', 5, 2);
    $table->enum('interest_type', ['flat', 'reducing_balance'])->default('reducing_balance');

    $table->unsignedSmallInteger('duration_months');

    $table->decimal('total_interest_due', 15, 2)->default(0);
    $table->decimal('total_amount_due', 15, 2)->default(0);
    $table->decimal('monthly_repayment_amount', 15, 2)->default(0);

    $table->decimal('amount_paid_to_date', 15, 2)->default(0);
    $table->decimal('outstanding_balance', 15, 2)->default(0);

    $table->decimal('penalty_rate', 5, 2)->nullable();

    $table->enum('status', [
        'pending',
        'approved',
        'disbursed',
        'active',
        'completed',
        'rejected',
        'defaulted'
    ])->default('pending');

    $table->string('purpose')->nullable();

    $table->date('application_date');
    $table->date('approval_date')->nullable();
    $table->date('disbursement_date')->nullable();
    $table->date('due_date')->nullable();

    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

    $table->text('notes')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
