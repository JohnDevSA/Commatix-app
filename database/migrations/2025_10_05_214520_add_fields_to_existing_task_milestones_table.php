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
            // Check if columns exist before adding them
            if (!Schema::hasColumn('task_milestones', 'sla_hours')) {
                $table->integer('sla_hours')->default(0)->after('sla_days');
            }
            
            if (!Schema::hasColumn('task_milestones', 'sla_minutes')) {
                $table->integer('sla_minutes')->default(0)->after('sla_hours');
            }
            
            if (!Schema::hasColumn('task_milestones', 'approval_group_name')) {
                $table->string('approval_group_name')->nullable()->after('approval_group_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_milestones', function (Blueprint $table) {
            if (Schema::hasColumn('task_milestones', 'sla_hours')) {
                $table->dropColumn('sla_hours');
            }
            
            if (Schema::hasColumn('task_milestones', 'sla_minutes')) {
                $table->dropColumn('sla_minutes');
            }
            
            if (Schema::hasColumn('task_milestones', 'approval_group_name')) {
                $table->dropColumn('approval_group_name');
            }
        });
    }
};