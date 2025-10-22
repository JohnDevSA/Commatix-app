<?php

namespace App\Services\Communication;

use App\Contracts\Services\TemplateRendererInterface;
use App\Models\MessageTemplate;
use App\Models\Subscriber;

class TemplateRendererService implements TemplateRendererInterface
{
    /**
     * Render template content with variables
     *
     * @param  array<string, mixed>  $variables  Custom variables
     * @return array<string, string> ['subject' => string|null, 'content' => string]
     */
    public function render(
        MessageTemplate $template,
        Subscriber $subscriber,
        array $variables = []
    ): array {
        // Build variable map
        $variableMap = $this->buildVariableMap($subscriber, $variables);

        // Render subject (if applicable)
        $subject = null;
        if ($template->needsSubject() && $template->subject) {
            $subject = $this->replaceVariables($template->subject, $variableMap);
        }

        // Render content
        $content = $this->replaceVariables($template->content, $variableMap);

        return [
            'subject' => $subject,
            'content' => $content,
        ];
    }

    /**
     * Validate template syntax
     *
     * @return array<string, mixed> ['valid' => bool, 'errors' => array]
     */
    public function validateTemplate(MessageTemplate $template): array
    {
        $errors = [];

        // Check if content is empty
        if (empty($template->content)) {
            $errors[] = 'Template content cannot be empty';
        }

        // Check if email template has subject
        if ($template->needsSubject() && empty($template->subject)) {
            $errors[] = 'Email templates must have a subject';
        }

        // Find all variables in content
        $contentVariables = $this->extractVariables($template->content);

        // Find all variables in subject (if applicable)
        $subjectVariables = [];
        if ($template->subject) {
            $subjectVariables = $this->extractVariables($template->subject);
        }

        $allVariables = array_unique(array_merge($contentVariables, $subjectVariables));

        // Check for unknown variables
        $availableVariables = array_keys($this->getAvailableVariables());
        $unknownVariables = array_diff($allVariables, $availableVariables);

        if (! empty($unknownVariables)) {
            $errors[] = 'Unknown variables: '.implode(', ', $unknownVariables);
        }

        // Check SMS length
        if ($template->isSms()) {
            $length = $template->getContentLength();
            if ($length > 1000) {
                $errors[] = "SMS content is too long ({$length} characters). Maximum recommended: 1000 characters";
            }

            $parts = $template->estimateSMSParts();
            if ($parts > 5) {
                $errors[] = "SMS will be split into {$parts} parts. Consider shortening the message.";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get available variables for templates
     *
     * @return array<string, string> Variable name => description
     */
    public function getAvailableVariables(): array
    {
        return [
            // Subscriber info
            'first_name' => 'Subscriber first name',
            'last_name' => 'Subscriber last name',
            'full_name' => 'Subscriber full name',
            'email' => 'Subscriber email address',
            'phone' => 'Subscriber phone number',

            // Tenant info
            'tenant_name' => 'Your organization name',
            'tenant_email' => 'Your organization email',
            'tenant_phone' => 'Your organization phone',

            // Campaign info
            'campaign_name' => 'Campaign name',
            'unsubscribe_url' => 'Unsubscribe link',

            // Date/Time
            'current_date' => 'Current date',
            'current_year' => 'Current year',
        ];
    }

    /**
     * Build variable map from subscriber and custom variables
     *
     * @param  array<string, mixed>  $customVariables
     * @return array<string, string>
     */
    private function buildVariableMap(Subscriber $subscriber, array $customVariables = []): array
    {
        $tenant = $subscriber->tenant;

        $map = [
            // Subscriber info
            'first_name' => $subscriber->first_name ?? '',
            'last_name' => $subscriber->last_name ?? '',
            'full_name' => trim(($subscriber->first_name ?? '').' '.($subscriber->last_name ?? '')),
            'email' => $subscriber->email ?? '',
            'phone' => $subscriber->phone ?? '',

            // Tenant info
            'tenant_name' => $tenant->name ?? '',
            'tenant_email' => $tenant->email ?? '',
            'tenant_phone' => $tenant->phone ?? '',

            // Date/Time
            'current_date' => now()->format('d/m/Y'), // South African format
            'current_year' => now()->format('Y'),

            // URLs (will be populated when campaign is known)
            'unsubscribe_url' => url('/unsubscribe/'.$subscriber->id),
        ];

        // Merge custom variables
        return array_merge($map, $customVariables);
    }

    /**
     * Replace variables in text
     *
     * @param  array<string, string>  $variables
     */
    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // Support both {{ variable }} and {variable} syntax
            $text = str_replace(['{{'.$key.'}}', '{'.$key.'}'], $value, $text);
        }

        return $text;
    }

    /**
     * Extract variables from text
     *
     * @return array<string>
     */
    private function extractVariables(string $text): array
    {
        $variables = [];

        // Match {{ variable }} syntax
        preg_match_all('/\{\{([a-z_]+)\}\}/', $text, $matches);
        if (! empty($matches[1])) {
            $variables = array_merge($variables, $matches[1]);
        }

        // Match {variable} syntax
        preg_match_all('/\{([a-z_]+)\}/', $text, $matches);
        if (! empty($matches[1])) {
            $variables = array_merge($variables, $matches[1]);
        }

        return array_unique($variables);
    }
}
