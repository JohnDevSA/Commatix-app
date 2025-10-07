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
        Schema::table('milestones', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->change();
            $table->foreignId('status_type_id')->nullable()->change();
            $table->integer('sla_days')->default(0)->change();
            $table->boolean('requires_docs')->default(false)->change();
            $table->json('actions')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable(false)->change();
            $table->foreignId('status_type_id')->nullable(false)->change();
            $table->integer('sla_days')->default(null)->change();
            $table->boolean('requires_docs')->default(null)->change();
            $table->json('actions')->nullable(false)->change();
        });
    }
};
