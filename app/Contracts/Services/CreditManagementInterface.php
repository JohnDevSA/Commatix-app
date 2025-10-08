<?php

namespace App\Contracts\Services;

use App\Models\Tenant;

interface CreditManagementInterface
{
    /**
     * Check if tenant has enough credits for a channel
     *
     * @param Tenant $tenant
     * @param string $channel (sms, email, whatsapp, voice)
     * @param int $amount Number of credits needed
     * @return bool
     */
    public function canUseChannel(Tenant $tenant, string $channel, int $amount): bool;

    /**
     * Deduct credits from tenant's account
     *
     * @param Tenant $tenant
     * @param string $channel (sms, email, whatsapp, voice)
     * @param int $amount Number of credits to deduct
     * @return bool
     * @throws \Exception If insufficient credits
     */
    public function deductCredits(Tenant $tenant, string $channel, int $amount): bool;

    /**
     * Add credits to tenant's account (top-up)
     *
     * @param Tenant $tenant
     * @param string $channel (sms, email, whatsapp, voice)
     * @param int $amount Number of credits to add
     * @param string|null $reason Reason for top-up
     * @return bool
     */
    public function addCredits(Tenant $tenant, string $channel, int $amount, ?string $reason = null): bool;

    /**
     * Get available credits for a channel
     *
     * @param Tenant $tenant
     * @param string $channel (sms, email, whatsapp, voice)
     * @return int
     */
    public function getAvailableCredits(Tenant $tenant, string $channel): int;

    /**
     * Get credit usage for current billing period
     *
     * @param Tenant $tenant
     * @param string $channel (sms, email, whatsapp, voice)
     * @return int
     */
    public function getCurrentUsage(Tenant $tenant, string $channel): int;
}