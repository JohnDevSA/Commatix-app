<?php

namespace App\Interfaces;

use App\Models\Tenant;
use App\Models\TenantSubscription;

interface CreditManagementInterface
{
    public function getTenantCredits(Tenant $tenant, string $channel): int;
    public function deductCredits(Tenant $tenant, string $channel, int $amount): bool;
    public function addCredits(Tenant $tenant, string $channel, int $amount): bool;
    public function canUseChannel(Tenant $tenant, string $channel, int $requiredCredits = 1): bool;
    public function getSubscriptionPackage(Tenant $tenant): ?TenantSubscription;
}