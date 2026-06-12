<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->string('status')->default('draft');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year'], 'pay_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
