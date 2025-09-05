<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $userTypes = [
            [
                'name' => 'Super Admin',
                'description' => 'Global platform administrator with access to all tenants and system configuration. Can impersonate users, manage providers, and access all features across tenants.'
            ],
            [
                'name' => 'Tenant Admin',
                'description' => 'Full administrative access within their tenant. Can manage users, configure settings, create campaigns, and access all tenant features.'
            ],
            [
                'name' => 'Tenant Manager',
                'description' => 'Management-level access within tenant. Can create and manage campaigns, view analytics, manage subscribers, but cannot modify tenant settings.'
            ],
            [
                'name' => 'Tenant User',
                'description' => 'Standard user access within tenant. Can create basic campaigns, manage assigned subscriber lists, and view own analytics.'
            ],
            [
                'name' => 'Tenant Viewer',
                'description' => 'Read-only access within tenant. Can view campaigns, analytics, and subscriber data but cannot create or modify anything.'
            ],
        ];

        foreach ($userTypes as $type) {
            UserType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('✅ User types created successfully');
    }
}
