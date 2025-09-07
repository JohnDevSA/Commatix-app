<?php

namespace Database\Seeders;

use App\Models\User;
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
            SouthAfricanBusinessSeeder::class,
            CommunicationProviderSeeder::class,
            GlobalTemplateSeeder::class,
            DocumentTypeSeeder::class,
            DemoTenantSeeder::class,
        ]);

        $this->command->info('🚀 Commatix Super Admin environment ready!');
        $this->command->info('📧 Super Admin Login: superadmin@commatix.io');
        $this->command->info('🔑 Password: CommatixSA2025!');
        $this->command->info('🏢 Demo tenants created for testing');
    }
}
