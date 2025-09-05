<?php

namespace Database\Seeders;

use App\Models\AccessScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $globalScope = AccessScope::where('name', 'global')->first();
        $industryScope = AccessScope::where('name', 'industry_template')->first();

        $templates = [
            // Global Welcome Series
            [
                'name' => 'Customer Welcome Series',
                'description' => 'Multi-channel welcome sequence for new customers',
                'access_scope_id' => $globalScope->id,
                'tenant_id' => null,
                'division_id' => null,
                'template_type' => 'global',
                'category' => 'onboarding',
                'industry' => 'general',
                'industry_category' => 'general',
                'channels' => json_encode(['email', 'sms']),
                'steps' => json_encode([
                    [
                        'step' => 1,
                        'delay_hours' => 0,
                        'channel' => 'email',
                        'subject' => 'Welcome to {company_name}!',
                        'content' => 'Welcome {first_name}! We\'re excited to have you on board.',
                    ],
                    [
                        'step' => 2,
                        'delay_hours' => 24,
                        'channel' => 'sms',
                        'content' => 'Hi {first_name}, this is {company_name}. Your account is ready!',
                    ],
                    [
                        'step' => 3,
                        'delay_hours' => 72,
                        'channel' => 'email',
                        'subject' => 'Getting started with {company_name}',
                        'content' => 'Here are some quick tips to get you started...',
                    ],
                ]),
                'tags' => json_encode(['welcome', 'onboarding', 'new_customer']),
                'is_active' => true,
                'is_system_template' => true,
                'is_customizable' => true,
                'complexity_level' => 'simple',
                'estimated_duration_days' => 3,
            ],

            // E-commerce Templates
            [
                'name' => 'E-commerce Abandoned Cart',
                'description' => 'Recover abandoned shopping carts with timed reminders',
                'access_scope_id' => $industryScope->id,
                'tenant_id' => null,
                'division_id' => null,
                'template_type' => 'industry',
                'category' => 'conversion',
                'industry' => 'ecommerce',
                'industry_category' => 'retail',
                'channels' => json_encode(['email', 'sms', 'whatsapp']),
                'steps' => json_encode([
                    [
                        'step' => 1,
                        'delay_hours' => 1,
                        'channel' => 'email',
                        'subject' => 'You left something in your cart',
                        'content' => 'Complete your purchase and save on {product_name}',
                    ],
                    [
                        'step' => 2,
                        'delay_hours' => 24,
                        'channel' => 'sms',
                        'content' => 'Still thinking about {product_name}? Get 10% off with code SAVE10',
                    ],
                    [
                        'step' => 3,
                        'delay_hours' => 72,
                        'channel' => 'whatsapp',
                        'content' => 'Last chance! Your {product_name} is waiting. Complete purchase now.',
                    ],
                ]),
                'tags' => json_encode(['cart_abandonment', 'ecommerce', 'conversion']),
                'is_active' => true,
                'is_system_template' => true,
                'is_customizable' => true,
                'complexity_level' => 'medium',
                'estimated_duration_days' => 3,
            ],

            // Financial Services Templates
            [
                'name' => 'Financial Services KYC',
                'description' => 'FICA compliant customer verification workflow',
                'access_scope_id' => $industryScope->id,
                'tenant_id' => null,
                'division_id' => null,
                'template_type' => 'industry',
                'category' => 'compliance',
                'industry' => 'financial_services',
                'industry_category' => 'finance',
                'channels' => json_encode(['email', 'sms']),
                'steps' => json_encode([
                    [
                        'step' => 1,
                        'delay_hours' => 0,
                        'channel' => 'email',
                        'subject' => 'Complete your FICA verification',
                        'content' => 'To comply with SA banking regulations, please submit your documents.',
                    ],
                    [
                        'step' => 2,
                        'delay_hours' => 48,
                        'channel' => 'sms',
                        'content' => 'FICA reminder: Please upload your ID and proof of address to complete verification.',
                    ],
                    [
                        'step' => 3,
                        'delay_hours' => 168, // 7 days
                        'channel' => 'email',
                        'subject' => 'Final reminder: FICA verification required',
                        'content' => 'Your account will be limited until FICA verification is complete.',
                    ],
                ]),
                'tags' => json_encode(['fica', 'kyc', 'compliance', 'banking']),
                'is_active' => true,
                'is_system_template' => true,
                'is_customizable' => false, // Compliance templates shouldn't be heavily modified
                'complexity_level' => 'complex',
                'estimated_duration_days' => 7,
                'locked_milestones' => json_encode(['fica_compliance']),
            ],

            // Healthcare Templates
            [
                'name' => 'Medical Appointment Reminders',
                'description' => 'Automated appointment reminders for healthcare providers',
                'access_scope_id' => $industryScope->id,
                'tenant_id' => null,
                'division_id' => null,
                'template_type' => 'industry',
                'category' => 'appointment',
                'industry' => 'healthcare',
                'industry_category' => 'medical',
                'channels' => json_encode(['email', 'sms', 'voice']),
                'steps' => json_encode([
                    [
                        'step' => 1,
                        'delay_hours' => -48, // 2 days before
                        'channel' => 'email',
                        'subject' => 'Appointment reminder - {appointment_date}',
                        'content' => 'Your appointment with Dr. {doctor_name} is scheduled for {appointment_date} at {appointment_time}.',
                    ],
                    [
                        'step' => 2,
                        'delay_hours' => -24, // 1 day before
                        'channel' => 'sms',
                        'content' => 'Reminder: Appointment tomorrow at {appointment_time} with Dr. {doctor_name}. Reply CONFIRM to confirm.',
                    ],
                    [
                        'step' => 3,
                        'delay_hours' => -2, // 2 hours before
                        'channel' => 'voice',
                        'content' => 'This is a reminder for your appointment with Dr. {doctor_name} at {appointment_time} today.',
                    ],
                ]),
                'tags' => json_encode(['appointment', 'healthcare', 'reminder']),
                'is_active' => true,
                'is_system_template' => true,
                'is_customizable' => true,
                'complexity_level' => 'medium',
                'estimated_duration_days' => 2,
            ],

            // Education Templates
            [
                'name' => 'Student Enrollment Journey',
                'description' => 'Guide prospective students through enrollment process',
                'access_scope_id' => $industryScope->id,
                'tenant_id' => null,
                'division_id' => null,
                'template_type' => 'industry',
                'category' => 'enrollment',
                'industry' => 'education',
                'industry_category' => 'academic',
                'channels' => json_encode(['email', 'whatsapp']),
                'steps' => json_encode([
                    [
                        'step' => 1,
                        'delay_hours' => 0,
                        'channel' => 'email',
                        'subject' => 'Thank you for your interest in {course_name}',
                        'content' => 'Next steps to secure your spot in {course_name}...',
                    ],
                    [
                        'step' => 2,
                        'delay_hours' => 72,
                        'channel' => 'whatsapp',
                        'content' => 'Hi {first_name}, have you had a chance to review the {course_name} requirements?',
                    ],
                    [
                        'step' => 3,
                        'delay_hours' => 168,
                        'channel' => 'email',
                        'subject' => 'Early bird discount ending soon - {course_name}',
                        'content' => 'Don\'t miss out on the early bird discount for {course_name}.',
                    ],
                ]),
                'tags' => json_encode(['education', 'enrollment', 'student']),
                'is_active' => true,
                'is_system_template' => true,
                'is_customizable' => true,
                'complexity_level' => 'medium',
                'estimated_duration_days' => 7,
            ],

            // Real Estate Templates
            [
                'name' => 'Property Lead Nurturing',
                'description' => 'Follow up with property inquiries and viewings',
                'access_scope_id' => $industryScope->id,
                'tenant_id' => null,
                'division_id' => null,
                'template_type' => 'industry',
                'category' => 'lead_nurturing',
                'industry' => 'real_estate',
                'industry_category' => 'property',
                'channels' => json_encode(['email', 'sms', 'whatsapp']),
                'steps' => json_encode([
                    [
                        'step' => 1,
                        'delay_hours' => 0,
                        'channel' => 'email',
                        'subject' => 'Your property inquiry - {property_address}',
                        'content' => 'Thank you for your interest in {property_address}. Here are the details...',
                    ],
                    [
                        'step' => 2,
                        'delay_hours' => 24,
                        'channel' => 'whatsapp',
                        'content' => 'Hi {first_name}, would you like to schedule a viewing for {property_address}?',
                    ],
                    [
                        'step' => 3,
                        'delay_hours' => 168,
                        'channel' => 'email',
                        'subject' => 'Similar properties you might like',
                        'content' => 'Since you were interested in {property_address}, here are similar properties...',
                    ],
                ]),
                'tags' => json_encode(['real_estate', 'property', 'lead_nurturing']),
                'is_active' => true,
                'is_system_template' => true,
                'is_customizable' => true,
                'complexity_level' => 'medium',
                'estimated_duration_days' => 7,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('workflow_templates')->insertOrIgnore($template);
        }

        $this->command->info('✅ Global workflow templates created');
        $this->command->info('📋 Templates: Welcome Series, Cart Abandonment, FICA KYC, Medical Reminders, Student Enrollment, Property Leads');
    }
}
