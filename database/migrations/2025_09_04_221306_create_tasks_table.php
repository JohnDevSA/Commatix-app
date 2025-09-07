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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('workflow_template_id')->constrained();
            $table->foreignId('subscriber_id')->constrained();
            $table->string('tenant_id'); // String for UUID tenants
            $table->foreignId('division_id')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->constrained('users');
            $table->enum('status', ['draft', 'scheduled', 'in_progress', 'on_hold', 'completed', 'cancelled'])->default('draft');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->date('scheduled_start_date');
            $table->datetime('actual_start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->foreignId('current_milestone_id')->nullable()->constrained('milestones');
            $table->text('early_start_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('scheduled_start_date');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
