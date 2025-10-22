<?php

namespace App\Contracts\Services;

use App\Models\MessageTemplate;
use App\Models\Subscriber;

interface TemplateRendererInterface
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
    ): array;

    /**
     * Validate template syntax
     *
     * @return array<string, mixed> ['valid' => bool, 'errors' => array]
     */
    public function validateTemplate(MessageTemplate $template): array;

    /**
     * Get available variables for templates
     *
     * @return array<string, string> Variable name => description
     */
    public function getAvailableVariables(): array;
}
