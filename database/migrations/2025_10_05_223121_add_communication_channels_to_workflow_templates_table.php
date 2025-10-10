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
        Schema::table('workflow_templates', function (Blueprint $table) {
            $table->boolean('email_enabled')->default(true)->after('description');
            $table->boolean('sms_enabled')->default(false)->after('email_enabled');
            $table->boolean('whatsapp_enabled')->default(false)->after('sms_enabled');
            $table->boolean('voice_enabled')->default(false)->after('whatsapp_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_templates', function (Blueprint $table) {
            $table->dropColumn(['email_enabled', 'sms_enabled', 'whatsapp_enabled', 'voice_enabled']);
        });
    }
};
