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
        Schema::create('task_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id');
            $table->unsignedBigInteger('status_id');
            $table->integer('sla_days');
            $table->unsignedBigInteger('approval_group_id')->nullable();
            $table->boolean('requires_docs');
            $table->json('actions');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('status_type_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_milestones');
    }
};
