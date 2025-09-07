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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('access_scope_id');
            $table->string('tenant_id')->nullable();
            
            // Additional configuration fields
            $table->string('industry_category')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('allows_multiple')->default(true);
            $table->integer('max_file_size_mb')->default(10);
            $table->json('allowed_file_types')->nullable();
            
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('access_scope_id');
            $table->index('industry_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
