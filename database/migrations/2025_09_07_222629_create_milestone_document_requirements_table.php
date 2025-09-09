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
        Schema::create('milestone_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(true);
            $table->boolean('allows_multiple')->default(false);
            $table->integer('sequence_order')->default(1);
            $table->text('instructions')->nullable();
            $table->text('validation_rules')->nullable();
            $table->timestamps();

            // Use shorter custom index names to avoid MySQL 64-character limit
            $table->unique(['milestone_id', 'document_type_id'], 'mdr_unique');
            $table->index(['milestone_id', 'sequence_order'], 'mdr_seq_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestone_document_requirements');
    }
};
