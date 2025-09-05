<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminType = UserType::where('name', 'Super Admin')->first();

        // Create Super Admin user (not tied to any tenant)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@commatix.io'],
            [
                'name' => 'Commatix Super Administrator',
                'password' => Hash::make('CommatixSA2025!'),
                'user_type_id' => $superAdminType->id,
                'tenant_id' => null, // Super admin not tied to any tenant
                'email_verified_at' => now(),
                'division_id' => null,
            ]
        );

        // Create additional super admin users for team
        User::firstOrCreate(
            ['email' => 'admin@commatix.io'],
            [
                'name' => 'Platform Administrator',
                'password' => Hash::make('CommatixAdmin2025!'),
                'user_type_id' => $superAdminType->id,
                'tenant_id' => null,
                'email_verified_at' => now(),
                'division_id' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'support@commatix.io'],
            [
                'name' => 'Customer Support Lead',
                'password' => Hash::make('CommatixSupport2025!'),
                'user_type_id' => $superAdminType->id,
                'tenant_id' => null,
                'email_verified_at' => now(),
                'division_id' => null,
            ]
        );

        $this->command->info('✅ Super Admin users created');
        $this->command->table(
            ['Email', 'Password', 'Role'],
            [
                ['superadmin@commatix.io', 'CommatixSA2025!', 'Super Admin'],
                ['admin@commatix.io', 'CommatixAdmin2025!', 'Platform Admin'],
                ['support@commatix.io', 'CommatixSupport2025!', 'Support Lead'],
            ]
        );
    }
}
