<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignid('user_id')->constrained()->onDelete('cascade');
            $table->string('member_no')->unique();

            // Personal Info
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->enum('gender', ['male', 'female',])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('national_id_number')->unique()->nullable();
            $table->string('passport_number')->nullable();
            $table->string('avatar')->nullable();

            // Contact Info
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();

            // Residential Address
            $table->text('address')->nullable();

            // Financial/Declaration
            $table->boolean('has_existing_savings')->default(false);
            $table->text('existing_savings_details')->nullable();
            $table->boolean('is_currently_in_debt')->default(false);
            $table->text('debt_details')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
