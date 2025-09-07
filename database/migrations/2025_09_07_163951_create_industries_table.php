<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique(); // e.g., 'financial_services'
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // For UI display
            $table->string('color')->default('#6366f1'); // For badge colors

            // South African specific
            $table->json('sic_codes')->nullable(); // Standard Industrial Classification codes
            $table->json('typical_compliance_requirements')->nullable(); // POPIA, B-BBEE, etc.
            $table->boolean('requires_fica')->default(false); // Financial services
            $table->boolean('requires_bee_compliance')->default(true);

            // Configuration
            $table->integer('typical_workflow_duration_days')->default(30);
            $table->json('common_document_types')->nullable(); // Suggested document types
            $table->json('regulatory_bodies')->nullable(); // e.g., FSB, SARS, etc.

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industries');
    }
};
