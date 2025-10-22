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
            
            $table->string('loan_product_name');
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2); // e.g., 12.00%
            $table->unsignedSmallInteger('repayment_period_months');
            $table->decimal('total_interest_due', 12, 2);
            $table->decimal('total_amount_due', 12, 2); // Principal + Interest
            $table->decimal('monthly_repayment_amount', 12, 2);

            $table->enum('status', ['pending', 'approved', 'disbursed', 'completed', 'defaulted'])->default('pending');

            $table->date('application_date');
            $table->date('approval_date')->nullable();
            $table->date('disbursement_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
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
