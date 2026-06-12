<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();

            $table->integer('days_worked');
            $table->decimal('daily_rate', 15, 2);
            $table->decimal('basic_salary_earned', 15, 2);

            $table->integer('meeting_count')->default(0);
            $table->decimal('meeting_allowance', 15, 2)->default(0);
            $table->decimal('qualification_allowance', 15, 2)->default(0);
            $table->decimal('recognition_allowance', 15, 2)->default(0);
            $table->decimal('other_allowances', 15, 2)->default(0);

            $table->decimal('gross_salary', 15, 2);

            $table->decimal('nssf_employee', 15, 2)->default(0);

            $table->decimal('taxable_income', 15, 2);

            $table->decimal('paye', 15, 2)->default(0);
            $table->decimal('lst', 15, 2)->default(0);

            $table->decimal('total_deductions', 15, 2);

            $table->decimal('net_salary', 15, 2);

            $table->decimal('savings_contribution', 15, 2);

            $table->decimal('advance_amount', 15, 2)->default(0);

            $table->decimal('final_take_home', 15, 2);

            $table->string('status')->default('draft');

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->unique(['payroll_profile_id', 'payroll_period_id'], 'pay_run_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
