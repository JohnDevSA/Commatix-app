<?php

namespace App\Services\Communication\Senders;

use App\Contracts\Services\MessageSenderInterface;
use App\Models\MessageTemplate;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Log;
use Resend\Laravel\Facades\Resend;

class EmailSenderService implements MessageSenderInterface
{
    /**
     * Send an email message
     *
     * @param  array<string, mixed>  $variables  Must include 'subject' and 'content'
     * @return array<string, mixed> ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function send(
        Subscriber $subscriber,
        MessageTemplate $template,
        array $variables = []
    ): array {
        try {
            $subject = $variables['subject'] ?? $template->subject ?? 'No Subject';
            $content = $variables['content'] ?? $template->content;

            // Get sender email from tenant or use default
            $fromEmail = $subscriber->tenant->email ?? config('mail.from.address');
            $fromName = $subscriber->tenant->name ?? config('mail.from.name');

            // Send email using Resend
            $result = Resend::emails()->send([
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => [$subscriber->email],
                'subject' => $subject,
                'html' => $this->wrapHtmlContent($content),
                'tags' => [
                    [
                        'name' => 'tenant_id',
                        'value' => (string) $subscriber->tenant_id,
                    ],
                    [
                        'name' => 'subscriber_id',
                        'value' => (string) $subscriber->id,
                    ],
                ],
            ]);

            return [
                'success' => true,
                'message_id' => $result->id ?? 'unknown',
                'provider' => 'resend',
                'response' => $result,
            ];
        } catch (\Exception $e) {
            Log::error("Email send failed for subscriber {$subscriber->id}: ".$e->getMessage());

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
        return 'email';
    }

    /**
     * Validate if email can be sent
     *
     * @return array<string, mixed> ['valid' => bool, 'error' => string|null]
     */
    public function validate(Subscriber $subscriber, MessageTemplate $template): array
    {
        if (empty($subscriber->email)) {
            return [
                'valid' => false,
                'error' => 'Subscriber has no email address',
            ];
        }

        if (! filter_var($subscriber->email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'error' => 'Subscriber email is invalid',
            ];
        }

        if (! $template->isEmail()) {
            return [
                'valid' => false,
                'error' => 'Template is not for email channel',
            ];
        }

        if (empty($template->subject)) {
            return [
                'valid' => false,
                'error' => 'Email template has no subject',
            ];
        }

        return [
            'valid' => true,
            'error' => null,
        ];
    }

    /**
     * Get cost per email (1 credit)
     */
    public function getCostPerMessage(): int
    {
        return 1;
    }

    /**
     * Wrap content in basic HTML template
     */
    private function wrapHtmlContent(string $content): string
    {
        // Check if content already has HTML structure
        if (stripos($content, '<html') !== false || stripos($content, '<!DOCTYPE') !== false) {
            return $content;
        }

        // Simple HTML wrapper with responsive design
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        p {
            margin-bottom: 15px;
        }
        a {
            color: #3b82f6;
            text-decoration: none;
        }
    </style>
</head>
<body>
    {$content}
</body>
</html>
HTML;
    }
}
