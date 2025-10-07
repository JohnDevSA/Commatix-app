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
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_template_id');
            $table->string('name');
            $table->unsignedBigInteger('status_id')->nullable();
            $table->text('hint')->nullable();
            $table->integer('sla_days')->default(0);
            $table->unsignedBigInteger('approval_group_id')->nullable();
            $table->boolean('requires_docs')->default(false);
            $table->json('actions')->nullable();
            $table->foreignId('status_type_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
