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
        Schema::create('data_consent_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('subscriber_id');
            $table->enum('consent_type', ["marketing","transactional","analytics","profiling"]);
            $table->boolean('consent_given');
            $table->timestamp('consent_date');
            $table->enum('consent_method', ["web_form","api","manual","import","phone","email"]);
            $table->string('consent_source')->nullable();
            $table->string('ip_address')->nullable();
            $table->enum('legal_basis', ["consent","legitimate_interest","contract","legal_obligation","vital_interests","public_task"]);
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_consent_records');
    }
};
