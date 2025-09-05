<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunicationProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            // Email Providers
            [
                'name' => 'Resend',
                'type' => 'email',
                'description' => 'Modern email API for developers',
                'is_active' => true,
                'is_default' => true,
                'configuration' => json_encode([
                    'api_key_required' => true,
                    'webhook_support' => true,
                    'pricing_model' => 'pay_per_send',
                    'max_recipients_per_send' => 50,
                    'supports_templates' => true,
                    'supports_tracking' => true,
                ]),
                'rate_limits' => json_encode([
                    'per_second' => 14,
                    'per_minute' => 600,
                    'per_hour' => 36000,
                    'per_day' => 864000,
                ]),
                'pricing' => json_encode([
                    'free_tier' => 3000,
                    'cost_per_1000' => 0.40, // USD
                ]),
            ],
            [
                'name' => 'SendGrid',
                'type' => 'email',
                'description' => 'Cloud-based email delivery platform',
                'is_active' => true,
                'is_default' => false,
                'configuration' => json_encode([
                    'api_key_required' => true,
                    'webhook_support' => true,
                    'pricing_model' => 'tiered',
                    'max_recipients_per_send' => 1000,
                    'supports_templates' => true,
                    'supports_tracking' => true,
                ]),
                'rate_limits' => json_encode([
                    'per_second' => 10,
                    'per_minute' => 600,
                    'per_hour' => 36000,
                ]),
                'pricing' => json_encode([
                    'free_tier' => 100,
                    'essentials_plan' => 19.95, // USD per month
                ]),
            ],

            // SMS Providers (SA-focused)
            [
                'name' => 'Vonage',
                'type' => 'sms',
                'description' => 'Global SMS API with SA support',
                'is_active' => true,
                'is_default' => true,
                'configuration' => json_encode([
                    'api_key_required' => true,
                    'api_secret_required' => true,
                    'webhook_support' => true,
                    'pricing_model' => 'pay_per_send',
                    'supports_unicode' => true,
                    'max_length' => 1600,
                ]),
                'rate_limits' => json_encode([
                    'per_second' => 1,
                    'per_minute' => 60,
                ]),
                'pricing' => json_encode([
                    'za_mobile' => 0.035, // USD per SMS to SA mobile
                    'za_fixed' => 0.035,
                ]),
            ],
            [
                'name' => 'ClickSend',
                'type' => 'sms',
                'description' => 'SMS service with good SA coverage',
                'is_active' => true,
                'is_default' => false,
                'configuration' => json_encode([
                    'username_required' => true,
                    'api_key_required' => true,
                    'webhook_support' => true,
                    'pricing_model' => 'credit_based',
                    'supports_unicode' => true,
                ]),
                'rate_limits' => json_encode([
                    'per_second' => 5,
                    'per_minute' => 300,
                ]),
                'pricing' => json_encode([
                    'za_mobile' => 0.048, // USD per SMS
                ]),
            ],

            // WhatsApp Providers
            [
                'name' => 'Twilio WhatsApp',
                'type' => 'whatsapp',
                'description' => 'Official WhatsApp Business API via Twilio',
                'is_active' => true,
                'is_default' => true,
                'configuration' => json_encode([
                    'account_sid_required' => true,
                    'auth_token_required' => true,
                    'webhook_support' => true,
                    'requires_approval' => true,
                    'template_required' => true,
                    'session_based' => true,
                ]),
                'rate_limits' => json_encode([
                    'conversation_based' => true,
                    'daily_limit' => 1000, // varies by approval
                ]),
                'pricing' => json_encode([
                    'conversation_rate' => 0.055, // USD per conversation
                ]),
            ],
            [
                'name' => 'Meta WhatsApp',
                'type' => 'whatsapp',
                'description' => 'Direct WhatsApp Business API from Meta',
                'is_active' => false,
                'is_default' => false,
                'configuration' => json_encode([
                    'access_token_required' => true,
                    'phone_number_id_required' => true,
                    'webhook_support' => true,
                    'requires_approval' => true,
                    'template_required' => true,
                ]),
                'rate_limits' => json_encode([
                    'conversation_based' => true,
                    'rate_limit_varies' => true,
                ]),
                'pricing' => json_encode([
                    'conversation_rate' => 0.055, // USD per conversation
                ]),
            ],

            // Voice Providers
            [
                'name' => 'Twilio Voice',
                'type' => 'voice',
                'description' => 'Programmable voice calls and IVR',
                'is_active' => true,
                'is_default' => true,
                'configuration' => json_encode([
                    'account_sid_required' => true,
                    'auth_token_required' => true,
                    'webhook_support' => true,
                    'supports_recording' => true,
                    'supports_ivr' => true,
                ]),
                'rate_limits' => json_encode([
                    'concurrent_calls' => 100, // varies by account
                ]),
                'pricing' => json_encode([
                    'za_outbound_mobile' => 0.095, // USD per minute
                    'za_outbound_landline' => 0.095,
                ]),
            ],
        ];

        foreach ($providers as $provider) {
            DB::table('communication_providers')->insertOrIgnore($provider);
        }

        $this->command->info('✅ Communication providers configured');
        $this->command->info('📧 Email: Resend (default), SendGrid');
        $this->command->info('📱 SMS: Vonage (default), ClickSend');
        $this->command->info('💬 WhatsApp: Twilio (default), Meta');
        $this->command->info('📞 Voice: Twilio');
    }
}
