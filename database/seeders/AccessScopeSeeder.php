<?php

namespace Database\Seeders;

use App\Models\AccessScope;
use Illuminate\Database\Seeder;

class AccessScopeSeeder extends Seeder
{
    public function run(): void
    {
        $scopes = [
            [
                'name' => 'global',
                'label' => 'Global System',
                'description' => 'Available across all tenants - managed by super admin'
            ],
            [
                'name' => 'tenant_custom',
                'label' => 'Tenant Custom',
                'description' => 'Custom content created by tenant users'
            ],
            [
                'name' => 'tenant_shared',
                'label' => 'Tenant Shared',
                'description' => 'Shared within tenant organization'
            ],
            [
                'name' => 'private',
                'label' => 'Private',
                'description' => 'Private to individual user'
            ],
            [
                'name' => 'industry_template',
                'label' => 'Industry Template',
                'description' => 'Industry-specific templates available to relevant tenants'
            ]
        ];

        foreach ($scopes as $scope) {
            AccessScope::firstOrCreate(
                ['name' => $scope['name']],
                $scope
            );
        }

        $this->command->info('✅ Access scopes created successfully');
    }
}
