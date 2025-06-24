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

        Schema::create('milestone_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id');
            $table->foreignId('milestone_activity_type_id')->constrained('');
            $table->text('message');
            $table->foreignId('user_id');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestone_activities');
    }
};
