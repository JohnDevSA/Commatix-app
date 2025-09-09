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
        Schema::table('workflow_templates', function (Blueprint $table) {
            // Workflow versioning system
            $table->string('workflow_code')->unique()->after('uuid');
            $table->string('version_number')->default('1.0')->after('template_version');
            $table->enum('version_status', ['draft', 'active', 'deprecated', 'archived'])->default('draft')->after('version_number');
            $table->foreignId('active_version_id')->nullable()->after('version_status');
            $table->foreignId('draft_version_id')->nullable()->after('active_version_id');
            $table->text('version_notes')->nullable()->after('change_log');
            $table->timestamp('version_created_at')->nullable()->after('version_notes');
            $table->unsignedBigInteger('version_created_by')->nullable()->after('version_created_at');

            // Enhanced publishing controls
            $table->boolean('requires_approval')->default(false)->after('is_published');
            $table->unsignedBigInteger('approved_by')->nullable()->after('requires_approval');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Usage tracking for versions
            $table->integer('active_tasks_count')->default(0)->after('usage_count');

            // Soft delete for version management
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_templates', function (Blueprint $table) {
            $table->dropColumn([
                'workflow_code',
                'version_number',
                'version_status',
                'active_version_id',
                'draft_version_id',
                'version_notes',
                'version_created_at',
                'version_created_by',
                'requires_approval',
                'approved_by',
                'approved_at',
                'active_tasks_count',
                'deleted_at'
            ]);
        });
    }
};
