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
        Schema::table('workflow_templates', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('is_active');
            $table->unsignedBigInteger('locked_by_user_id')->nullable()->after('is_locked');
            $table->timestamp('locked_at')->nullable()->after('locked_by_user_id');
            $table->string('lock_reason')->nullable()->after('locked_at');
            $table->boolean('milestones_completed')->default(false)->after('lock_reason');
            
            $table->foreign('locked_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_templates', function (Blueprint $table) {
            $table->dropForeign(['locked_by_user_id']);
            $table->dropColumn([
                'is_locked',
                'locked_by_user_id', 
                'locked_at',
                'lock_reason',
                'milestones_completed'
            ]);
        });
    }
};
