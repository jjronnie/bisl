<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->foreignId('bulk_sms_campaign_id')
                ->nullable()
                ->after('recipient_id')
                ->constrained('bulk_sms_campaigns')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropForeign(['bulk_sms_campaign_id']);
            $table->dropColumn('bulk_sms_campaign_id');
        });
    }
};
