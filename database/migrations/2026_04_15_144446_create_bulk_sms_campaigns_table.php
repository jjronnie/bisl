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
        Schema::create('bulk_sms_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('message');
            $table->integer('total_recipients');
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->decimal('total_cost', 10, 4)->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->unsignedBigInteger('created_by');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_sms_campaigns');
    }
};
