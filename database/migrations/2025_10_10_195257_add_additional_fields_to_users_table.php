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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('position')->nullable()->after('division_id');
            $table->string('preferred_language')->default('en')->after('position');
            $table->boolean('two_factor_enabled')->default(false)->after('preferred_language');
            $table->boolean('is_active')->default(true)->after('two_factor_enabled');
            $table->boolean('email_notifications')->default(true)->after('is_active');
            $table->boolean('sms_notifications')->default(true)->after('email_notifications');
            $table->boolean('marketing_emails')->default(false)->after('sms_notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'position',
                'preferred_language',
                'two_factor_enabled',
                'is_active',
                'email_notifications',
                'sms_notifications',
                'marketing_emails',
            ]);
        });
    }
};
