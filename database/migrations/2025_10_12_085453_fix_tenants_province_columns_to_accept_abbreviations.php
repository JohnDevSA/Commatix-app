<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Convert ENUM columns to VARCHAR temporarily to allow data migration
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('physical_province', 50)->change();
            $table->string('postal_province', 50)->nullable()->change();
        });

        // Step 2: Migrate existing data from snake_case to abbreviations
        DB::table('tenants')->update([
            'physical_province' => DB::raw("CASE physical_province
                WHEN 'gauteng' THEN 'GP'
                WHEN 'western_cape' THEN 'WC'
                WHEN 'kwazulu_natal' THEN 'KZN'
                WHEN 'eastern_cape' THEN 'EC'
                WHEN 'northern_cape' THEN 'NC'
                WHEN 'free_state' THEN 'FS'
                WHEN 'limpopo' THEN 'LP'
                WHEN 'mpumalanga' THEN 'MP'
                WHEN 'north_west' THEN 'NW'
                ELSE physical_province
            END"),
        ]);

        // Update postal_province if it has snake_case values
        DB::table('tenants')
            ->whereNotNull('postal_province')
            ->update([
                'postal_province' => DB::raw("CASE postal_province
                    WHEN 'gauteng' THEN 'GP'
                    WHEN 'western_cape' THEN 'WC'
                    WHEN 'kwazulu_natal' THEN 'KZN'
                    WHEN 'eastern_cape' THEN 'EC'
                    WHEN 'northern_cape' THEN 'NC'
                    WHEN 'free_state' THEN 'FS'
                    WHEN 'limpopo' THEN 'LP'
                    WHEN 'mpumalanga' THEN 'MP'
                    WHEN 'north_west' THEN 'NW'
                    ELSE postal_province
                END"),
            ]);

        // Step 3: Convert VARCHAR to ENUM with new abbreviation values
        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('physical_province', ['EC', 'FS', 'GP', 'KZN', 'LP', 'MP', 'NC', 'NW', 'WC'])
                ->change();

            $table->enum('postal_province', ['EC', 'FS', 'GP', 'KZN', 'LP', 'MP', 'NC', 'NW', 'WC'])
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Revert physical_province back to original ENUM values
            $table->enum('physical_province', ['gauteng', 'western_cape', 'kwazulu_natal', 'eastern_cape', 'northern_cape', 'free_state', 'limpopo', 'mpumalanga', 'north_west'])
                ->change();

            // Revert postal_province back to string
            $table->string('postal_province')->nullable()->change();
        });
    }
};
