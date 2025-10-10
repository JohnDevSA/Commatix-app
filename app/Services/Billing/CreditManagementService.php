<?php

namespace App\Services\Billing;

use App\Contracts\Services\CreditManagementInterface;
use App\Models\Tenant;
use App\Models\TenantTopUp;
use App\Models\TenantUsage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditManagementService implements CreditManagementInterface
{
    private const VALID_CHANNELS = ['sms', 'email', 'whatsapp', 'voice'];

    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Check if tenant has enough credits for a channel
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     * @param  int  $amount  Number of credits needed
     */
    public function canUseChannel(Tenant $tenant, string $channel, int $amount): bool
    {
        $this->validateChannel($channel);

        $available = $this->getAvailableCredits($tenant, $channel);

        return $available >= $amount;
    }

    /**
     * Deduct credits from tenant's account
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     * @param  int  $amount  Number of credits to deduct
     *
     * @throws \Exception If insufficient credits
     */
    public function deductCredits(Tenant $tenant, string $channel, int $amount): bool
    {
        $this->validateChannel($channel);

        if (! $this->canUseChannel($tenant, $channel, $amount)) {
            throw new \Exception("Insufficient {$channel} credits. Available: {$this->getAvailableCredits($tenant, $channel)}, Required: {$amount}");
        }

        try {
            DB::beginTransaction();

            // Get or create current period usage record
            $usage = $this->getCurrentPeriodUsage($tenant);

            // Update usage based on channel
            $columnName = "{$channel}_sent";
            $usage->increment($columnName, $amount);

            // Clear cache
            $this->clearCreditCache($tenant, $channel);

            DB::commit();

            Log::info("Deducted {$amount} {$channel} credits from tenant {$tenant->name}");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to deduct credits: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Add credits to tenant's account (top-up)
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     * @param  int  $amount  Number of credits to add
     * @param  string|null  $reason  Reason for top-up
     */
    public function addCredits(Tenant $tenant, string $channel, int $amount, ?string $reason = null): bool
    {
        $this->validateChannel($channel);

        try {
            // Create top-up record
            TenantTopUp::create([
                'tenant_id' => $tenant->id,
                'channel' => $channel,
                'amount' => $amount,
                'reason' => $reason ?? 'Manual top-up',
                'added_by' => auth()->id(),
                'created_at' => now(),
            ]);

            // Clear cache
            $this->clearCreditCache($tenant, $channel);

            Log::info("Added {$amount} {$channel} credits to tenant {$tenant->name}. Reason: {$reason}");

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to add credits: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Get available credits for a channel
     *
     * Available = Subscription Limit + Top-ups - Current Usage
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     */
    public function getAvailableCredits(Tenant $tenant, string $channel): int
    {
        $this->validateChannel($channel);

        return Cache::remember(
            $this->getCacheKey($tenant, $channel),
            self::CACHE_TTL,
            function () use ($tenant, $channel) {
                // Get subscription limit for this channel
                $subscription = $tenant->activeSubscription;
                $limit = $subscription ? $this->getChannelLimit($subscription, $channel) : 0;

                // Get total top-ups for current period
                $topUps = $this->getCurrentPeriodTopUps($tenant, $channel);

                // Get current usage
                $usage = $this->getCurrentUsage($tenant, $channel);

                // Available = Limit + Top-ups - Usage
                $available = ($limit + $topUps) - $usage;

                return max(0, $available); // Never return negative
            }
        );
    }

    /**
     * Get credit usage for current billing period
     *
     * @param  string  $channel  (sms, email, whatsapp, voice)
     */
    public function getCurrentUsage(Tenant $tenant, string $channel): int
    {
        $this->validateChannel($channel);

        $usage = $this->getCurrentPeriodUsage($tenant);
        $columnName = "{$channel}_sent";

        return $usage ? $usage->{$columnName} : 0;
    }

    /**
     * Get current billing period usage record
     */
    private function getCurrentPeriodUsage(Tenant $tenant): TenantUsage
    {
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();

        return TenantUsage::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ],
            [
                'emails_sent' => 0,
                'sms_sent' => 0,
                'whatsapp_sent' => 0,
                'voice_calls' => 0,
                'storage_used_mb' => 0,
                'api_calls' => 0,
            ]
        );
    }

    /**
     * Get total top-ups for current period
     */
    private function getCurrentPeriodTopUps(Tenant $tenant, string $channel): int
    {
        $periodStart = now()->startOfMonth();

        return TenantTopUp::where('tenant_id', $tenant->id)
            ->where('channel', $channel)
            ->where('created_at', '>=', $periodStart)
            ->sum('amount');
    }

    /**
     * Get channel limit from subscription
     *
     * @param  \App\Models\TenantSubscription  $subscription
     */
    private function getChannelLimit($subscription, string $channel): int
    {
        // Map channel to subscription field
        $limitMap = [
            'sms' => 'sms_limit',
            'email' => 'email_limit',
            'whatsapp' => 'whatsapp_limit',
            'voice' => 'voice_limit',
        ];

        $limitField = $limitMap[$channel] ?? null;

        return $limitField && isset($subscription->{$limitField})
            ? $subscription->{$limitField}
            : 0;
    }

    /**
     * Validate channel name
     *
     * @throws \InvalidArgumentException
     */
    private function validateChannel(string $channel): void
    {
        if (! in_array($channel, self::VALID_CHANNELS)) {
            throw new \InvalidArgumentException("Invalid channel: {$channel}. Valid channels: ".implode(', ', self::VALID_CHANNELS));
        }
    }

    /**
     * Get cache key for credits
     */
    private function getCacheKey(Tenant $tenant, string $channel): string
    {
        return "credits:tenant_{$tenant->id}:channel_{$channel}";
    }

    /**
     * Clear credit cache for a tenant and channel
     */
    private function clearCreditCache(Tenant $tenant, string $channel): void
    {
        Cache::forget($this->getCacheKey($tenant, $channel));
    }
}
