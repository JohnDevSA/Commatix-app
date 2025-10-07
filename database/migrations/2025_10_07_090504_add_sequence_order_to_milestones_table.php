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
            $table->integer('sequence_order')->default(0)->after('name');
            $table->text('description')->nullable()->after('sequence_order');
            $table->integer('estimated_duration_days')->nullable()->after('description');
            $table->string('milestone_type')->nullable()->after('estimated_duration_days');
            $table->string('priority')->default('medium')->after('milestone_type');
            $table->boolean('requires_approval')->default(false)->after('approval_group_name');
            $table->boolean('can_be_skipped')->default(false)->after('requires_approval');
            $table->boolean('auto_complete')->default(false)->after('can_be_skipped');
            $table->json('completion_criteria')->nullable()->after('auto_complete');
            $table->text('notes')->nullable()->after('actions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn([
                'sequence_order',
                'description',
                'estimated_duration_days',
                'milestone_type',
                'priority',
                'requires_approval',
                'can_be_skipped',
                'auto_complete',
                'completion_criteria',
                'notes'
            ]);
        });
    }
};
