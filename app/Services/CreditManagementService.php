<?php

namespace App\Services;

use App\Interfaces\CreditManagementInterface;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\TenantUsage;

class CreditManagementService implements CreditManagementInterface
{
    /**
     * Get the current credits for a tenant and channel
     */
    public function getTenantCredits(Tenant $tenant, string $channel): int
    {
        $subscription = $this->getSubscriptionPackage($tenant);
        
        if (!$subscription) {
            return 0;
        }
        
        // Get usage for this channel
        $usage = TenantUsage::where('tenant_id', $tenant->id)
            ->where('channel', $channel)
            ->where('period', now()->format('Y-m'))
            ->first();
        
        $usedCredits = $usage ? $usage->credits_used : 0;
        
        // Get allowed credits from subscription
        $allowedField = "max_{$channel}_per_month";
        $allowedCredits = $subscription->{$allowedField} ?? 0;
        
        return max(0, $allowedCredits - $usedCredits);
    }

    /**
     * Deduct credits from a tenant's balance
     */
    public function deductCredits(Tenant $tenant, string $channel, int $amount): bool
    {
        if (!$this->canUseChannel($tenant, $channel, $amount)) {
            return false;
        }
        
        // Record the usage
        TenantUsage::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'channel' => $channel,
                'period' => now()->format('Y-m')
            ],
            [
                'credits_used' => \DB::raw("credits_used + $amount"),
                'last_updated' => now()
            ]
        );
        
        return true;
    }

    /**
     * Add credits to a tenant's balance (for top-ups)
     */
    public function addCredits(Tenant $tenant, string $channel, int $amount): bool
    {
        $subscription = $this->getSubscriptionPackage($tenant);
        
        if (!$subscription) {
            return false;
        }
        
        // For top-ups, we might want to create a special record or adjust the subscription
        // This is a simplified implementation
        $topUpField = "top_up_{$channel}";
        
        // In a real implementation, you might have a separate top-up table
        // For now, we'll just record that credits were added
        
        \DB::table('tenant_top_ups')->insert([
            'tenant_id' => $tenant->id,
            'channel' => $channel,
            'credits' => $amount,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return true;
    }

    /**
     * Check if a tenant can use a channel (has sufficient credits)
     */
    public function canUseChannel(Tenant $tenant, string $channel, int $requiredCredits = 1): bool
    {
        $availableCredits = $this->getTenantCredits($tenant, $channel);
        return $availableCredits >= $requiredCredits;
    }

    /**
     * Get the subscription package for a tenant
     */
    public function getSubscriptionPackage(Tenant $tenant): ?TenantSubscription
    {
        return $tenant->tenantSubscriptions()
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();
    }
}