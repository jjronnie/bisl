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
        Schema::create('loan_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('installment_id')->constrained('loan_installments')->cascadeOnDelete();
            $table->enum('type', ['due_today', 'due_in_7_days']); // Type of reminder
            $table->enum('channel', ['email', 'sms']); // Channel used to send
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('loan_id');
            $table->index('installment_id');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_reminders');
    }
};
