<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Subscriber;
use App\Models\SubscriberList;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Use Admin type for tenant admins, User type for tenant users
        $tenantAdminType = UserType::where('name', 'Admin')->first();
        $tenantUserType = UserType::where('name', 'User')->first();

        // Get domain suffix from environment (e.g., .dev, .stage, .co.za)
        $domainSuffix = config('app.tenant_domain_suffix', '.local');

        // Create Demo Tenants for different SA business scenarios
        $demoTenants = [
            [
                'tenant_data' => [
                    'id' => Str::uuid()->toString(),
                    'name' => 'TechStartup SA (Pty) Ltd',
                    'trading_name' => 'TechStartup SA',
                    'unique_code' => 'TECH001',
                    'company_registration_number' => '2023/123456/07',
                    'vat_number' => '4123456789',
                    'tax_reference_number' => '9001234567',
                    'bee_level' => '4',
                    'industry_classification' => '61100',
                    'company_type' => 'pty_ltd',
                    'primary_contact_person' => 'John Smith',
                    'primary_email' => 'john@techstartup.co.za',
                    'primary_phone' => '+27123456789',
                    'physical_address_line1' => '123 Innovation Drive',
                    'physical_city' => 'Sandton',
                    'physical_province' => 'gauteng',
                    'physical_postal_code' => '2196',
                    'subscription_tier' => 'business',
                    'billing_cycle' => 'monthly',
                    'max_users' => 25,
                    'max_subscribers' => 10000,
                    'max_campaigns_per_month' => 50,
                    'max_emails_per_month' => 25000,
                    'max_sms_per_month' => 5000,
                    'max_whatsapp_per_month' => 2500,
                    'allowed_channels' => ['email', 'sms', 'whatsapp'],
                    'default_sender_name' => 'TechStartup SA',
                    'default_sender_email' => 'noreply@techstartup.co.za',
                    'popia_consent_obtained' => true,
                    'popia_consent_date' => now(),
                    'business_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                    'status' => 'active',
                    'is_verified' => true,
                    'onboarding_completed' => true,
                    'verified_at' => now(),
                ],
                'domain' => 'techstartup.commatix'.$domainSuffix,
                'users' => [
                    [
                        'name' => 'John Smith',
                        'email' => 'john@techstartup.co.za',
                        'password' => 'TechDemo2025!',
                        'user_type_id' => $tenantAdminType->id,
                    ],
                    [
                        'name' => 'Sarah Wilson',
                        'email' => 'sarah@techstartup.co.za',
                        'password' => 'TechDemo2025!',
                        'user_type_id' => $tenantUserType->id,
                    ],
                ],
                'divisions' => ['Marketing', 'Sales', 'Development'],
                'subscriber_lists' => ['Newsletter', 'Product Updates', 'Beta Testers'],
            ],

            [
                'tenant_data' => [
                    'id' => Str::uuid()->toString(),
                    'name' => 'Cape Finance Solutions (Pty) Ltd',
                    'trading_name' => 'Cape Finance',
                    'unique_code' => 'FIN001',
                    'company_registration_number' => '2020/987654/07',
                    'vat_number' => '4987654321',
                    'tax_reference_number' => '9009876543',
                    'bee_level' => '2',
                    'industry_classification' => '64110',
                    'company_type' => 'pty_ltd',
                    'primary_contact_person' => 'Michael van der Merwe',
                    'primary_email' => 'michael@capefinance.co.za',
                    'primary_phone' => '+27214567890',
                    'physical_address_line1' => '456 Financial Plaza',
                    'physical_city' => 'Cape Town',
                    'physical_province' => 'western_cape',
                    'physical_postal_code' => '8001',
                    'subscription_tier' => 'enterprise',
                    'billing_cycle' => 'annually',
                    'max_users' => 100,
                    'max_subscribers' => 50000,
                    'max_campaigns_per_month' => 200,
                    'max_emails_per_month' => 100000,
                    'max_sms_per_month' => 20000,
                    'max_whatsapp_per_month' => 10000,
                    'allowed_channels' => ['email', 'sms', 'whatsapp', 'voice'],
                    'default_sender_name' => 'Cape Finance',
                    'default_sender_email' => 'noreply@capefinance.co.za',
                    'popia_consent_obtained' => true,
                    'popia_consent_date' => now()->subMonths(6),
                    'gdpr_applicable' => true,
                    'business_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                    'status' => 'active',
                    'is_verified' => true,
                    'onboarding_completed' => true,
                    'verified_at' => now()->subMonths(6),
                ],
                'domain' => 'capefinance.commatix'.$domainSuffix,
                'users' => [
                    [
                        'name' => 'Michael van der Merwe',
                        'email' => 'michael@capefinance.co.za',
                        'password' => 'FinanceDemo2025!',
                        'user_type_id' => $tenantAdminType->id,
                    ],
                    [
                        'name' => 'Lisa Naidoo',
                        'email' => 'lisa@capefinance.co.za',
                        'password' => 'FinanceDemo2025!',
                        'user_type_id' => $tenantUserType->id,
                    ],
                ],
                'divisions' => ['Retail Banking', 'Corporate Banking', 'Compliance', 'Customer Service'],
                'subscriber_lists' => ['Account Holders', 'Loan Applicants', 'Investment Clients'],
            ],

            [
                'tenant_data' => [
                    'id' => Str::uuid()->toString(),
                    'name' => 'Durban Health Network (Pty) Ltd',
                    'trading_name' => 'DurbanHealth',
                    'unique_code' => 'HEALTH001',
                    'company_registration_number' => '2019/456789/07',
                    'vat_number' => '4456789123',
                    'tax_reference_number' => '9004567891',
                    'bee_level' => '3',
                    'industry_classification' => '86101',
                    'company_type' => 'pty_ltd',
                    'primary_contact_person' => 'Dr. Priya Patel',
                    'primary_email' => 'admin@durbanhealth.co.za',
                    'primary_phone' => '+27313456789',
                    'physical_address_line1' => '789 Medical Centre',
                    'physical_city' => 'Durban',
                    'physical_province' => 'kwazulu_natal',
                    'physical_postal_code' => '4001',
                    'subscription_tier' => 'business',
                    'billing_cycle' => 'monthly',
                    'max_users' => 50,
                    'max_subscribers' => 25000,
                    'max_campaigns_per_month' => 100,
                    'max_emails_per_month' => 50000,
                    'max_sms_per_month' => 15000,
                    'max_whatsapp_per_month' => 5000,
                    'allowed_channels' => ['email', 'sms', 'voice'],
                    'default_sender_name' => 'Durban Health',
                    'default_sender_email' => 'appointments@durbanhealth.co.za',
                    'popia_consent_obtained' => true,
                    'popia_consent_date' => now()->subMonths(3),
                    'gdpr_applicable' => false,
                    'business_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                    'business_hours_start' => '07:00:00',
                    'business_hours_end' => '19:00:00',
                    'status' => 'active',
                    'is_verified' => true,
                    'onboarding_completed' => true,
                    'verified_at' => now()->subMonths(3),
                ],
                'domain' => 'durbanhealth.commatix'.$domainSuffix,
                'users' => [
                    [
                        'name' => 'Dr. Priya Patel',
                        'email' => 'priya@durbanhealth.co.za',
                        'password' => 'HealthDemo2025!',
                        'user_type_id' => $tenantAdminType->id,
                    ],
                    [
                        'name' => 'Nurse Amanda Johnson',
                        'email' => 'amanda@durbanhealth.co.za',
                        'password' => 'HealthDemo2025!',
                        'user_type_id' => $tenantUserType->id,
                    ],
                ],
                'divisions' => ['General Practice', 'Specialists', 'Emergency', 'Administration'],
                'subscriber_lists' => ['Patients', 'Appointment Reminders', 'Health Tips'],
            ],
        ];

        foreach ($demoTenants as $demoData) {
            $this->createDemoTenant($demoData);
        }

        $this->command->info('✅ Demo tenants created successfully');
        $this->command->table(
            ['Company', 'Domain', 'Admin Email', 'Password', 'Industry'],
            [
                ['TechStartup SA', 'techstartup.commatix'.$domainSuffix, 'john@techstartup.co.za', 'TechDemo2025!', 'Tech/IT'],
                ['Cape Finance', 'capefinance.commatix'.$domainSuffix, 'michael@capefinance.co.za', 'FinanceDemo2025!', 'Financial Services'],
                ['Durban Health', 'durbanhealth.commatix'.$domainSuffix, 'priya@durbanhealth.co.za', 'HealthDemo2025!', 'Healthcare'],
            ]
        );
    }

    private function createDemoTenant(array $demoData): void
    {
        // Create the tenant using explicit columns
        $tenant = Tenant::create($demoData['tenant_data']);

        // Create domain for tenant
        $tenant->domains()->create([
            'domain' => $demoData['domain'],
        ]);

        // Switch to tenant context to create tenant-specific data
        tenancy()->initialize($tenant);

        // Create divisions
        $divisions = [];
        foreach ($demoData['divisions'] as $divisionName) {
            $division = Division::create([
                'name' => $divisionName,
                'tenant_id' => $tenant->id,
            ]);
            $divisions[] = $division;
        }

        // Create users
        foreach ($demoData['users'] as $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'user_type_id' => $userData['user_type_id'],
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
                'division_id' => $divisions[0]->id ?? null,
            ]);
        }

        // Create subscriber lists
        foreach ($demoData['subscriber_lists'] as $listName) {
            $subscriberList = SubscriberList::create([
                'name' => $listName,
                'description' => "Demo subscriber list for {$listName}",
                'tenant_id' => $tenant->id,
                'total_subscribers' => 0,
                'active_subscribers' => 0,
                'is_public' => false,
            ]);

            // Add some demo subscribers
            $this->createDemoSubscribers($subscriberList, $tenant->id);
        }

        // End tenant context
        tenancy()->end();
    }

    private function createDemoSubscribers(SubscriberList $subscriberList, string $tenantId): void
    {
        $demoSubscribers = [
            [
                'email' => 'demo1@example.com',
                'phone' => '+27821234567',
                'whatsapp' => '+27821234567',
                'first_name' => 'Alice',
                'last_name' => 'Mokoena',
                'status' => 'active',
                'source' => 'manual',
                'tags' => ['demo', 'active_customer'],
            ],
            [
                'email' => 'demo2@example.com',
                'phone' => '+27827654321',
                'whatsapp' => '+27827654321',
                'first_name' => 'David',
                'last_name' => 'Williams',
                'status' => 'active',
                'source' => 'web_form',
                'tags' => ['demo', 'newsletter'],
            ],
            [
                'email' => 'demo3@example.com',
                'phone' => '+27829876543',
                'first_name' => 'Nomsa',
                'last_name' => 'Dlamini',
                'status' => 'active',
                'source' => 'import',
                'tags' => ['demo', 'vip'],
            ],
        ];

        foreach ($demoSubscribers as $subscriberData) {
            Subscriber::create([
                'email' => $subscriberData['email'],
                'phone' => $subscriberData['phone'],
                'whatsapp' => $subscriberData['whatsapp'] ?? null,
                'first_name' => $subscriberData['first_name'],
                'last_name' => $subscriberData['last_name'],
                'tenant_id' => $tenantId,
                'subscriber_list_id' => $subscriberList->id,
                'status' => $subscriberData['status'],
                'source' => $subscriberData['source'],
                'tags' => json_encode($subscriberData['tags']),
                'custom_fields' => json_encode([]),
            ]);
        }

        // Update subscriber counts
        $subscriberList->update([
            'total_subscribers' => count($demoSubscribers),
            'active_subscribers' => count($demoSubscribers),
        ]);
    }
}
