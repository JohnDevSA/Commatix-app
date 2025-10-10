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
                'description' => 'Global platform administrator with access to all tenants and system configuration. Can impersonate users, manage providers, and access all features across tenants.',
                'is_super_admin' => true,
            ],
            [
                'name' => 'Admin',
                'description' => 'Full administrative access within their organization. Can manage users, configure settings, create workflows, and access all features.',
                'is_super_admin' => false,
            ],
            [
                'name' => 'Manager',
                'description' => 'Management-level access within organization. Can create and manage workflows, view analytics, manage team members, but cannot modify organization settings.',
                'is_super_admin' => false,
            ],
            [
                'name' => 'Team Lead',
                'description' => 'Team leadership access within organization. Can manage assigned tasks, view team analytics, and coordinate workflow execution.',
                'is_super_admin' => false,
            ],
            [
                'name' => 'User',
                'description' => 'Standard user access within organization. Can create and manage assigned tasks, participate in workflows, and view own analytics.',
                'is_super_admin' => false,
            ],
            [
                'name' => 'Viewer',
                'description' => 'Read-only access within organization. Can view workflows, analytics, and task data but cannot create or modify anything.',
                'is_super_admin' => false,
            ],
        ];

        foreach ($userTypes as $type) {
            UserType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('✅ User types created successfully');
    }
}
