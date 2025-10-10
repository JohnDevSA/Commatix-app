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
        Schema::create('industry_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('industry', ['finance', 'healthcare', 'retail', 'manufacturing', 'education', 'government', 'nonprofit', 'technology']);
            $table->text('description');
            $table->foreignId('workflow_template_id');
            $table->json('compliance_requirements')->nullable();
            $table->integer('typical_duration_days');
            $table->integer('complexity_score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_templates');
    }
};
