<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add strategic indexes for frequently queried columns
     */
    public function up(): void
    {
        // Subscriber lists - frequently filtered by tenant
        Schema::table('subscriber_lists', function (Blueprint $table) {
            if (! $this->indexExists('subscriber_lists', 'subscriber_lists_tenant_id_idx')) {
                $table->index('tenant_id', 'subscriber_lists_tenant_id_idx');
            }
        });

        // Workflow templates - filtered by tenant and active status
        Schema::table('workflow_templates', function (Blueprint $table) {
            if (! $this->indexExists('workflow_templates', 'workflow_templates_tenant_active_idx')) {
                $table->index(['tenant_id', 'is_active'], 'workflow_templates_tenant_active_idx');
            }
            if (! $this->indexExists('workflow_templates', 'workflow_templates_industry_active_idx')) {
                $table->index(['industry', 'is_active'], 'workflow_templates_industry_active_idx');
            }
            if (! $this->indexExists('workflow_templates', 'workflow_templates_version_status_idx')) {
                $table->index('version_status', 'workflow_templates_version_status_idx');
            }
        });

        // Milestones - frequently joined with workflow_template
        Schema::table('milestones', function (Blueprint $table) {
            if (! $this->indexExists('milestones', 'milestones_workflow_sequence_idx')) {
                $table->index(['workflow_template_id', 'sequence_order'], 'milestones_workflow_sequence_idx');
            }
        });

        // Task milestones - critical for workflow progression
        Schema::table('task_milestones', function (Blueprint $table) {
            if (! $this->indexExists('task_milestones', 'task_milestones_task_status_idx')) {
                $table->index(['task_id', 'status'], 'task_milestones_task_status_idx');
            }
            if (! $this->indexExists('task_milestones', 'task_milestones_milestone_id_idx')) {
                $table->index('milestone_id', 'task_milestones_milestone_id_idx');
            }
        });

        // Approval groups - filtered by tenant and division
        Schema::table('approval_groups', function (Blueprint $table) {
            if (! $this->indexExists('approval_groups', 'approval_groups_tenant_division_idx')) {
                $table->index(['tenant_id', 'division_id'], 'approval_groups_tenant_division_idx');
            }
        });

        // Subscribers - add subscriber_list_id and status indexes
        Schema::table('subscribers', function (Blueprint $table) {
            if (! $this->indexExists('subscribers', 'subscribers_list_id_idx')) {
                $table->index('subscriber_list_id', 'subscribers_list_id_idx');
            }
            if (! $this->indexExists('subscribers', 'subscribers_tenant_list_idx')) {
                $table->index(['tenant_id', 'subscriber_list_id'], 'subscribers_tenant_list_idx');
            }
            if (! $this->indexExists('subscribers', 'subscribers_status_idx')) {
                $table->index('status', 'subscribers_status_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriber_lists', function (Blueprint $table) {
            $table->dropIndex('subscriber_lists_tenant_id_idx');
        });

        Schema::table('workflow_templates', function (Blueprint $table) {
            $table->dropIndex('workflow_templates_tenant_active_idx');
            $table->dropIndex('workflow_templates_industry_active_idx');
            $table->dropIndex('workflow_templates_version_status_idx');
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->dropIndex('milestones_workflow_sequence_idx');
        });

        Schema::table('task_milestones', function (Blueprint $table) {
            $table->dropIndex('task_milestones_task_status_idx');
            $table->dropIndex('task_milestones_milestone_id_idx');
        });

        Schema::table('approval_groups', function (Blueprint $table) {
            $table->dropIndex('approval_groups_tenant_division_idx');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex('subscribers_list_id_idx');
            $table->dropIndex('subscribers_tenant_list_idx');
            $table->dropIndex('subscribers_status_idx');
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
