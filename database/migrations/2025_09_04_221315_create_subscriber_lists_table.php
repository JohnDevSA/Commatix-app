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
        Schema::create('subscriber_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('tenant_id'); // Changed from foreignId to string
            $table->integer('total_subscribers')->default(0);
            $table->integer('active_subscribers')->default(0);
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriber_lists');
    }
};
