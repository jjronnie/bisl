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
      Schema::create('employment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained()->onDelete('cascade');

            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->unsignedSmallInteger('period')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('proof_path')->nullable();

            // Employer Address
            $table->text('address')->nullable();
           

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_details');
    }
};
