<?php

namespace App\Contracts\Services;

use App\Models\MessageTemplate;
use App\Models\Subscriber;

interface MessageSenderInterface
{
    /**
     * Send a message to a subscriber
     *
     * @param  array<string, mixed>  $variables  Template variables
     * @return array<string, mixed> ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function send(
        Subscriber $subscriber,
        MessageTemplate $template,
        array $variables = []
    ): array;

    /**
     * Get the channel this sender handles
     */
    public function getChannel(): string;

    /**
     * Validate if message can be sent
     *
     * @return array<string, mixed> ['valid' => bool, 'error' => string|null]
     */
    public function validate(Subscriber $subscriber, MessageTemplate $template): array;

    /**
     * Get cost per message for this channel
     */
    public function getCostPerMessage(): int;
}
