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
            $table->string('tenant_id'); // Changed from foreignId to string
            $table->foreignId('user_id');
            $table->timestamp('used_at');
            $table->string('usage_type')->default('copy');
            $table->text('customizations')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('workflow_template_id');
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
