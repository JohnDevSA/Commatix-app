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
        Schema::table('milestone_document_requirements', function (Blueprint $table) {
            // Drop the problematic index if it exists
            $table->dropIndex(['milestone_id', 'sequence_order']);

            // Add it back with a shorter name
            $table->index(['milestone_id', 'sequence_order'], 'mdr_milestone_sequence_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestone_document_requirements', function (Blueprint $table) {
            $table->dropIndex('mdr_milestone_sequence_idx');
            $table->index(['milestone_id', 'sequence_order']);
        });
    }
};
