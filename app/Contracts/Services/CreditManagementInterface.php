<?php

namespace App\Contracts\Services;

use App\Models\Tenant;

interface CreditManagementInterface
{
    /**
     * Check if tenant has enough credits for a channel
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     * @param  int  $amount  Number of credits needed
     */
    public function canUseChannel(Tenant $tenant, string $channel, int $amount): bool;

    /**
     * Deduct credits from tenant's account
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     * @param  int  $amount  Number of credits to deduct
     *
     * @throws \Exception If insufficient credits
     */
    public function deductCredits(Tenant $tenant, string $channel, int $amount): bool;

    /**
     * Add credits to tenant's account (top-up)
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     * @param  int  $amount  Number of credits to add
     * @param  string|null  $reason  Reason for top-up
     */
    public function addCredits(Tenant $tenant, string $channel, int $amount, ?string $reason = null): bool;

    /**
     * Get available credits for a channel
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     */
    public function getAvailableCredits(Tenant $tenant, string $channel): int;

    /**
     * Get credit usage for current billing period
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     */
    public function getCurrentUsage(Tenant $tenant, string $channel): int;
}
