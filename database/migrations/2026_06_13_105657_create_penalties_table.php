<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('type');
            $table->date('meeting_date')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
