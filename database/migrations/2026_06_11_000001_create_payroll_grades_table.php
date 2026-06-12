<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_grades', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('monthly_basic_salary', 15, 2);
            $table->integer('working_days_divisor')->default(30);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_grades');
    }
};
