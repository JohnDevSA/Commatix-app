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

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('workflow_template_id');
            $table->unsignedBigInteger('status_id');
            $table->foreignId('tenant_id');
            $table->foreignId('division_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('assigned_to');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->foreignId('status_type_id');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
