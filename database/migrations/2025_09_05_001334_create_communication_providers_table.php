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
        Schema::create('communication_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['email', 'sms', 'whatsapp', 'voice']);
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('configuration')->nullable();
            $table->json('rate_limits')->nullable();
            $table->json('pricing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_providers');
    }
};
