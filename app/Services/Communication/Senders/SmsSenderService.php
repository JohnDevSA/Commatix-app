<?php

namespace App\Services\Communication\Senders;

use App\Contracts\Services\MessageSenderInterface;
use App\Models\MessageTemplate;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Log;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class SmsSenderService implements MessageSenderInterface
{
    private Client $vonageClient;

    public function __construct()
    {
        $this->vonageClient = new Client(
            new Basic(
                config('services.vonage.key'),
                config('services.vonage.secret')
            )
        );
    }

    /**
     * Send an SMS message
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
            $content = $variables['content'] ?? $template->content;

            // Get sender name from tenant (max 11 chars for alphanumeric sender ID)
            $from = substr($subscriber->tenant->name ?? config('app.name'), 0, 11);

            // Format phone number (ensure it starts with +)
            $to = $this->formatPhoneNumber($subscriber->phone);

            // Send SMS
            $message = new SMS($to, $from, $content);
            $response = $this->vonageClient->sms()->send($message);

            $current = $response->current();

            if ($current->getStatus() == 0) {
                return [
                    'success' => true,
                    'message_id' => $current->getMessageId(),
                    'provider' => 'vonage',
                    'response' => [
                        'status' => $current->getStatus(),
                        'to' => $current->getTo(),
                        'remaining_balance' => $current->getRemainingBalance(),
                        'message_price' => $current->getMessagePrice(),
                        'network' => $current->getNetwork(),
                    ],
                ];
            } else {
                return [
                    'success' => false,
                    'message_id' => null,
                    'error' => 'SMS send failed with status: '.$current->getStatus(),
                    'response' => [
                        'status' => $current->getStatus(),
                    ],
                ];
            }
        } catch (\Exception $e) {
            Log::error("SMS send failed for subscriber {$subscriber->id}: ".$e->getMessage());

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
        return 'sms';
    }

    /**
     * Validate if SMS can be sent
     *
     * @return array<string, mixed> ['valid' => bool, 'error' => string|null]
     */
    public function validate(Subscriber $subscriber, MessageTemplate $template): array
    {
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

        if (! $template->isSms()) {
            return [
                'valid' => false,
                'error' => 'Template is not for SMS channel',
            ];
        }

        // Check SMS length
        $length = $template->getContentLength();
        if ($length > 1600) {
            return [
                'valid' => false,
                'error' => "SMS content is too long ({$length} characters). Maximum: 1600",
            ];
        }

        return [
            'valid' => true,
            'error' => null,
        ];
    }

    /**
     * Get cost per SMS (based on message parts)
     */
    public function getCostPerMessage(): int
    {
        // For now, charge 1 credit per SMS regardless of parts
        // TODO: Calculate based on template estimateSMSParts()
        return 1;
    }

    /**
     * Format phone number to E.164 format
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
