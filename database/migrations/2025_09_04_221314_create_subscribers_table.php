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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('tenant_id'); // Changed from foreignId to string
            $table->foreignId('subscriber_list_id');
            $table->enum('status', ['active', 'inactive', 'unsubscribed', 'bounced'])->default('active');
            $table->timestamp('opt_out_date')->nullable();
            $table->enum('source', ['manual', 'import', 'api', 'web_form'])->nullable();
            $table->json('tags')->nullable();
            $table->json('custom_fields')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['email', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
