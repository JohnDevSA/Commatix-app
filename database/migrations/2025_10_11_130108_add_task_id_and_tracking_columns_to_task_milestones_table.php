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
        Schema::table('task_milestones', function (Blueprint $table) {
            // Add task_id foreign key - CRITICAL for hasMany relationship
            $table->foreignId('task_id')
                ->after('id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            // Add sequence_order (copied from milestone template)
            if (! Schema::hasColumn('task_milestones', 'sequence_order')) {
                $table->integer('sequence_order')->default(0)->after('milestone_id');
            }

            // Add status tracking
            if (! Schema::hasColumn('task_milestones', 'status')) {
                $table->string('status')->default('pending')->after('sequence_order');
            }

            // Add started_at timestamp
            if (! Schema::hasColumn('task_milestones', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('completed_at');
            }

            // Add completed_by user tracking
            if (! Schema::hasColumn('task_milestones', 'completed_by')) {
                $table->foreignId('completed_by')
                    ->nullable()
                    ->after('started_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Add completion_notes
            if (! Schema::hasColumn('task_milestones', 'completion_notes')) {
                $table->text('completion_notes')->nullable()->after('completed_by');
            }

            // Add indexes for performance
            $table->index(['task_id', 'status'], 'task_milestones_task_status_idx');
            $table->index(['task_id', 'sequence_order'], 'task_milestones_task_sequence_idx');
            $table->index('milestone_id', 'task_milestones_milestone_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_milestones', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('task_milestones_task_status_idx');
            $table->dropIndex('task_milestones_task_sequence_idx');
            $table->dropIndex('task_milestones_milestone_idx');

            // Drop columns in reverse order
            if (Schema::hasColumn('task_milestones', 'completion_notes')) {
                $table->dropColumn('completion_notes');
            }

            if (Schema::hasColumn('task_milestones', 'completed_by')) {
                $table->dropForeign(['completed_by']);
                $table->dropColumn('completed_by');
            }

            if (Schema::hasColumn('task_milestones', 'started_at')) {
                $table->dropColumn('started_at');
            }

            if (Schema::hasColumn('task_milestones', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('task_milestones', 'sequence_order')) {
                $table->dropColumn('sequence_order');
            }

            // Drop task_id foreign key and column last
            $table->dropForeign(['task_id']);
            $table->dropColumn('task_id');
        });
    }
};
