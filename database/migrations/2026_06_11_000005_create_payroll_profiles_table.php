<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('payroll_grade_id')->constrained()->cascadeOnDelete();
            $table->string('employee_number')->unique();
            $table->string('employment_type');
            $table->string('qualification_level')->default('certificate');
            $table->string('recognition_level')->default('none');
            $table->boolean('meeting_allowance_eligible')->default(false);
            $table->date('employment_start_date');
            $table->date('employment_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('employment_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_profiles');
    }
};
