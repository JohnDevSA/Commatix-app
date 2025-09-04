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
            $table->foreignId('division_id');
            $table->text('description');
            $table->unsignedBigInteger('status_id');
            $table->foreignId('access_scope_id');
            $table->enum('template_type', ["system","industry","custom","copied"])->default('custom');
            $table->foreignId('parent_template_id')->nullable();
            $table->string('industry_category')->nullable();
            $table->string('template_version')->default('1.0');
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_system_template')->default(false);
            $table->string('tenant_id')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->json('tags')->nullable();
            $table->integer('estimated_duration_days')->nullable();
            $table->enum('complexity_level', ["simple","medium","complex"])->default('medium');
            $table->boolean('is_customizable')->default(true);
            $table->json('locked_milestones')->nullable();
            $table->json('required_roles')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->text('change_log')->nullable();
            $table->foreignId('status_type_id');
            $table->foreignId('user_id');
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
