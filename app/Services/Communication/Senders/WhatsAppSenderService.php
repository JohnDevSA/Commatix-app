<?php

namespace App\Services\Communication\Senders;

use App\Contracts\Services\MessageSenderInterface;
use App\Models\MessageTemplate;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppSenderService implements MessageSenderInterface
{
    private string $apiKey;

    private string $apiSecret;

    private string $fromNumber;

    public function __construct()
    {
        $this->apiKey = config('services.vonage.key');
        $this->apiSecret = config('services.vonage.secret');
        $this->fromNumber = config('services.vonage.whatsapp_number', ''); // WhatsApp Business number
    }

    /**
     * Send a WhatsApp message using Vonage Messages API
     *
     * @param  array<string, mixed>  $variables  Must include 'content'
     * @return array<string, mixed> ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function send(
        Subscriber $subscriber,
        MessageTemplate $template,
        array $variables = []
    ): array {
        try {
            if (empty($this->fromNumber)) {
                return [
                    'success' => false,
                    'message_id' => null,
                    'error' => 'WhatsApp sender number not configured. Set VONAGE_WHATSAPP_NUMBER in .env',
                ];
            }

            $content = $variables['content'] ?? $template->content;
            $to = $this->formatPhoneNumber($subscriber->phone);

            // Use Vonage Messages API v1
            $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
                ->post('https://messages-sandbox.nexmo.com/v1/messages', [
                    'from' => $this->fromNumber,
                    'to' => $to,
                    'channel' => 'whatsapp',
                    'message_type' => 'text',
                    'text' => $content,
                    'client_ref' => 'commatix-'.$subscriber->id,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'message_id' => $data['message_uuid'] ?? 'unknown',
                    'provider' => 'vonage_whatsapp',
                    'response' => $data,
                ];
            } else {
                $error = $response->json();

                return [
                    'success' => false,
                    'message_id' => null,
                    'error' => $error['title'] ?? 'WhatsApp send failed',
                    'response' => $error,
                ];
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed for subscriber {$subscriber->id}: ".$e->getMessage());

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get the channel this sender handles
     */
    public function getChannel(): string
    {
        return 'whatsapp';
    }

    /**
     * Validate if WhatsApp message can be sent
     *
     * @return array<string, mixed> ['valid' => bool, 'error' => string|null]
     */
    public function validate(Subscriber $subscriber, MessageTemplate $template): array
    {
        if (empty($this->fromNumber)) {
            return [
                'valid' => false,
                'error' => 'WhatsApp sender number not configured',
            ];
        }

        if (empty($subscriber->phone)) {
            return [
                'valid' => false,
                'error' => 'Subscriber has no phone number',
            ];
        }

        if (! $this->isValidPhoneNumber($subscriber->phone)) {
            return [
                'valid' => false,
                'error' => 'Subscriber phone number is invalid',
            ];
        }

        if (! $template->isWhatsApp()) {
            return [
                'valid' => false,
                'error' => 'Template is not for WhatsApp channel',
            ];
        }

        // WhatsApp has a 4096 character limit
        $length = $template->getContentLength();
        if ($length > 4096) {
            return [
                'valid' => false,
                'error' => "WhatsApp content is too long ({$length} characters). Maximum: 4096",
            ];
        }

        return [
            'valid' => true,
            'error' => null,
        ];
    }

    /**
     * Get cost per WhatsApp message
     */
    public function getCostPerMessage(): int
    {
        // WhatsApp messages typically cost more than SMS
        return 1;
    }

    /**
     * Format phone number to E.164 format (required by WhatsApp)
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // If it doesn't start with +, add South African country code
        if (! str_starts_with($phone, '+')) {
            // Remove leading 0 if present
            $phone = ltrim($phone, '0');
            $phone = '+27'.$phone;
        }

        return $phone;
    }

    /**
     * Validate phone number format
     */
    private function isValidPhoneNumber(string $phone): bool
    {
        $formatted = $this->formatPhoneNumber($phone);

        // Must start with + and have 10-15 digits
        return preg_match('/^\+[1-9]\d{9,14}$/', $formatted) === 1;
    }
}
