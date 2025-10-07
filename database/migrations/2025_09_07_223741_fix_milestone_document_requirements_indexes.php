<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('milestone_document_requirements', function (Blueprint $table) {
            // Check if the index exists before trying to drop it
            $indexes = DB::select('SHOW INDEX FROM milestone_document_requirements WHERE Key_name = "milestone_document_requirements_milestone_id_sequence_order_index"');
            
            if (!empty($indexes)) {
                $table->dropIndex(['milestone_id', 'sequence_order']);
            }

            // Check if the new index already exists
            $newIndexes = DB::select('SHOW INDEX FROM milestone_document_requirements WHERE Key_name = "mdr_milestone_sequence_idx"');
            
            if (empty($newIndexes)) {
                // Add it back with a shorter name
                $table->index(['milestone_id', 'sequence_order'], 'mdr_milestone_sequence_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestone_document_requirements', function (Blueprint $table) {
            // Check if the new index exists before dropping it
            $newIndexes = DB::select('SHOW INDEX FROM milestone_document_requirements WHERE Key_name = "mdr_milestone_sequence_idx"');
            
            if (!empty($newIndexes)) {
                $table->dropIndex('mdr_milestone_sequence_idx');
            }
            
            // Check if the old index exists before adding it back
            $indexes = DB::select('SHOW INDEX FROM milestone_document_requirements WHERE Key_name = "milestone_document_requirements_milestone_id_sequence_order_index"');
            
            if (empty($indexes)) {
                $table->index(['milestone_id', 'sequence_order']);
            }
        });
    }
};