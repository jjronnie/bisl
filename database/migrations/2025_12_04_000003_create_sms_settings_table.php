<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // App-wide SMS settings (not per-user)
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('payment_notifications_enabled')->default(true);
            $table->boolean('loan_status_notifications_enabled')->default(true);
            $table->boolean('transaction_alerts_enabled')->default(true);
            $table->timestamps();
        });

        // Insert default settings (single row only)
        DB::table('sms_settings')->insert([
            'payment_notifications_enabled' => true,
            'loan_status_notifications_enabled' => true,
            'transaction_alerts_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};
