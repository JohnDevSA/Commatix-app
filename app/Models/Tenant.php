<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'subscription_start_date' => 'date',
            'subscription_end_date' => 'date',
            'allowed_channels' => 'array',
            'popia_consent_obtained' => 'boolean',
            'popia_consent_date' => 'timestamp',
            'gdpr_applicable' => 'boolean',
            'business_days' => 'array',
            'is_verified' => 'boolean',
            'verification_documents' => 'array',
            'onboarding_completed' => 'boolean',
            'monthly_spend_limit' => 'decimal',
            'current_month_spend' => 'decimal',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'verified_at' => 'timestamp',
            'last_active_at' => 'timestamp',
            'suspended_at' => 'timestamp',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscriber::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function messageTemplates(): HasMany
    {
        return $this->hasMany(MessageTemplate::class);
    }

    public function tenantSubscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function tenantUsages(): HasMany
    {
        return $this->hasMany(TenantUsage::class);
    }

    public function tenantAuditLogs(): HasMany
    {
        return $this->hasMany(TenantAuditLog::class);
    }
}
