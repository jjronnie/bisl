<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nssf_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->decimal('employee_amount', 15, 2);
            $table->decimal('employer_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('pending');
            $table->timestamp('remitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nssf_ledger_entries');
    }
};
