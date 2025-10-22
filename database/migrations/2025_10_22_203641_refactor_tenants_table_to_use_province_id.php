<?php

use App\Models\Province;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Refactor tenants table to use foreign key relationship with sa_provinces table
     * instead of storing province abbreviations directly.
     */
    public function up(): void
    {
        // Step 1: Add the new physical_province_id column (nullable during migration)
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('physical_province_id')
                ->nullable()
                ->after('physical_city')
                ->constrained('sa_provinces')
                ->onDelete('restrict');
        });

        // Step 2: Migrate existing data from physical_province to physical_province_id
        // Map province codes to IDs
        $provinceMap = Province::pluck('id', 'code')->toArray();

        if (! empty($provinceMap)) {
            // Update existing records
            DB::table('tenants')->whereNotNull('physical_province')->get()->each(function ($tenant) use ($provinceMap) {
                $provinceCode = strtoupper($tenant->physical_province);

                if (isset($provinceMap[$provinceCode])) {
                    DB::table('tenants')
                        ->where('id', $tenant->id)
                        ->update(['physical_province_id' => $provinceMap[$provinceCode]]);
                }
            });
        }

        // Step 3: Drop the old physical_province column
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('physical_province');
        });

        // Step 4: Make physical_province_id non-nullable (optional - depends on business rules)
        // Uncomment if province should always be required:
        // Schema::table('tenants', function (Blueprint $table) {
        //     $table->foreignId('physical_province_id')->nullable(false)->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Re-add the old physical_province column
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('physical_province', 50)
                ->nullable()
                ->after('physical_city');
        });

        // Step 2: Migrate data back from physical_province_id to physical_province
        $provinces = Province::all();

        foreach ($provinces as $province) {
            DB::table('tenants')
                ->where('physical_province_id', $province->id)
                ->update(['physical_province' => $province->code]);
        }

        // Step 3: Drop the physical_province_id column
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['physical_province_id']);
            $table->dropColumn('physical_province_id');
        });
    }
};
