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
        Schema::create('workflow_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->text('description');

            // Core template relationships
            $table->foreignId('access_scope_id');
            $table->string('tenant_id')->nullable(); // String for UUID tenants
            $table->foreignId('division_id')->nullable(); // Make nullable for global templates

            // Template categorization
            $table->enum('template_type', ['system', 'industry', 'custom', 'copied', 'global'])->default('custom');
            $table->foreignId('parent_template_id')->nullable();
            $table->string('industry_category')->nullable();
            $table->string('category')->nullable(); // For seeder compatibility
            $table->string('industry')->nullable(); // For seeder compatibility
            $table->string('template_version')->default('1.0');

            // Communication channels and workflow steps
            $table->json('channels')->nullable(); // For seeder compatibility
            $table->json('steps')->nullable(); // For seeder compatibility

            // Status and workflow management
            $table->unsignedBigInteger('status_id')->nullable(); // Make nullable for global templates
            $table->foreignId('status_type_id')->nullable(); // Make nullable for global templates

            // User and permissions
            $table->unsignedBigInteger('created_by')->nullable(); // Make nullable for global templates
            $table->foreignId('user_id')->nullable(); // Make nullable for global templates
            $table->boolean('is_public')->default(false);
            $table->boolean('is_system_template')->default(false);

            // Usage tracking
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->json('tags')->nullable();

            // Template metadata
            $table->integer('estimated_duration_days')->nullable();
            $table->enum('complexity_level', ['simple', 'medium', 'complex'])->default('medium');
            $table->boolean('is_customizable')->default(true);
            $table->json('locked_milestones')->nullable();
            $table->json('required_roles')->nullable();

            // Publishing and versioning
            $table->boolean('is_published')->default(false);
            $table->boolean('is_active')->default(true); // For seeder compatibility
            $table->timestamp('published_at')->nullable();
            $table->text('change_log')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_templates');
    }
};
