<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_meeting_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $table->integer('meetings_attended');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['payroll_profile_id', 'payroll_period_id'], 'pay_meet_attend_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_meeting_attendance');
    }
};
