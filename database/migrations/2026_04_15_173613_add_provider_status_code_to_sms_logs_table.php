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
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->string('provider_status_code', 20)->nullable()->after('status');
            $table->string('provider_status_message', 255)->nullable()->after('provider_status_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn(['provider_status_code', 'provider_status_message']);
        });
    }
};
