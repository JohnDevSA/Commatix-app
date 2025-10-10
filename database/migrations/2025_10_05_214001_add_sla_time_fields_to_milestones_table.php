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
            $table->integer('sla_hours')->default(0)->after('sla_days');
            $table->integer('sla_minutes')->default(0)->after('sla_hours');
            $table->string('approval_group_name')->nullable()->after('approval_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn(['sla_hours', 'sla_minutes', 'approval_group_name']);
        });
    }
};
