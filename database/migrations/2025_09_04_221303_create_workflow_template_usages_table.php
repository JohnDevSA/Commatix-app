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
        Schema::create('workflow_template_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_template_id');
            $table->foreignId('tenant_id');
            $table->foreignId('user_id');
            $table->enum('action', ["copied","used","modified","published"]);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_template_usages');
    }
};
