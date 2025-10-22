<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run in specific order due to dependencies
        $this->call([
            UserTypeSeeder::class,
            AccessScopeSeeder::class,
            SuperAdminSeeder::class,
            ProvinceSeeder::class,              // Added: SA Provinces (must run before tenants)
            IndustrySeeder::class,              // Added: Must run before workflows/documents
            SouthAfricanBusinessSeeder::class,
            CommunicationProviderSeeder::class,
            DocumentTypeSeeder::class,
            ComprehensiveWorkflowSeeder::class, // Added: Modern workflow templates with milestones
            DemoTenantSeeder::class,
        ]);

        $this->command->info('🚀 Commatix Super Admin environment ready!');
        $this->command->info('📧 Super Admin Login: superadmin@commatix.io');
        $this->command->info('🔑 Password: CommatixSA2025!');
        $this->command->info('🏢 Demo tenants created for testing');
        $this->command->info('🏭 10 SA industries seeded');
        $this->command->info('📋 5 comprehensive workflow templates created');
    }
}
