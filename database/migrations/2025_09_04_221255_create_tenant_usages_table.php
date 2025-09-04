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
        Schema::create('tenant_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->integer('emails_sent')->default(0);
            $table->integer('sms_sent')->default(0);
            $table->integer('whatsapp_sent')->default(0);
            $table->integer('voice_calls')->default(0);
            $table->decimal('storage_used_mb')->default(0);
            $table->integer('api_calls')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_usages');
    }
};
