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
        Schema::disableForeignKeyConstraints();

        Schema::create('task_milestone_activity_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_milestone_id')->constrained('taskmilestones');
            $table->string('name');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_milestone_activity_types');
    }
};
