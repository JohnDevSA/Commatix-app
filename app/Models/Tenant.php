<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains;

    // Override the table if needed
    protected $table = 'tenants';

    // Use explicit columns instead of storing everything in data JSON
    public static $customColumns = [
        'id',
        'name',
        'trading_name',
        'unique_code',
        'company_registration_number',
        'vat_number',
        'tax_reference_number',
        'bee_level',
        'industry_classification',
        'company_type',
        'primary_contact_person',
        'primary_email',
        'primary_phone',
        'billing_contact_person',
        'billing_email',
        'billing_phone',
        'physical_address_line1',
        'physical_address_line2',
        'physical_city',
        'physical_province',
        'physical_postal_code',
        'postal_address_line1',
        'postal_address_line2',
        'postal_city',
        'postal_province',
        'postal_code',
        'subscription_tier',
        'billing_cycle',
        'subscription_start_date',
        'subscription_end_date',
        'max_users',
        'max_subscribers',
        'max_campaigns_per_month',
        'max_emails_per_month',
        'max_sms_per_month',
        'max_whatsapp_per_month',
        'allowed_channels',
        'default_sender_name',
        'default_sender_email',
        'default_sender_phone',
        'communication_timezone',
        'popia_consent_obtained',
        'popia_consent_date',
        'data_retention_period_days',
        'gdpr_applicable',
        'privacy_policy_url',
        'terms_of_service_url',
        'business_hours_start',
        'business_hours_end',
        'business_days',
        'status',
        'is_verified',
        'verification_documents',
        'onboarding_completed',
        'onboarding_step',
        'monthly_spend_limit',
        'current_month_spend',
        'currency',
        'verified_at',
        'last_active_at',
        'suspended_at',
    ];

    protected $fillable = [
        'id',
        'name',
        'trading_name',
        'unique_code',
        'company_registration_number',
        'vat_number',
        'tax_reference_number',
        'bee_level',
        'industry_classification',
        'company_type',
        'primary_contact_person',
        'primary_email',
        'primary_phone',
        'billing_contact_person',
        'billing_email',
        'billing_phone',
        'physical_address_line1',
        'physical_address_line2',
        'physical_city',
        'physical_province',
        'physical_postal_code',
        'postal_address_line1',
        'postal_address_line2',
        'postal_city',
        'postal_province',
        'postal_code',
        'subscription_tier',
        'billing_cycle',
        'subscription_start_date',
        'subscription_end_date',
        'max_users',
        'max_subscribers',
        'max_campaigns_per_month',
        'max_emails_per_month',
        'max_sms_per_month',
        'max_whatsapp_per_month',
        'allowed_channels',
        'default_sender_name',
        'default_sender_email',
        'default_sender_phone',
        'communication_timezone',
        'popia_consent_obtained',
        'popia_consent_date',
        'data_retention_period_days',
        'gdpr_applicable',
        'privacy_policy_url',
        'terms_of_service_url',
        'business_hours_start',
        'business_hours_end',
        'business_days',
        'status',
        'is_verified',
        'verification_documents',
        'onboarding_completed',
        'onboarding_step',
        'monthly_spend_limit',
        'current_month_spend',
        'currency',
        'verified_at',
        'last_active_at',
        'suspended_at',
    ];

    // Specify which columns should NOT be stored in the data JSON field
    protected $guarded = ['data'];

    protected function casts(): array
    {
        return [
            'id' => 'string',
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
            'monthly_spend_limit' => 'decimal:2',
            'current_month_spend' => 'decimal:2',
            'verified_at' => 'timestamp',
            'last_active_at' => 'timestamp',
            'suspended_at' => 'timestamp',
        ];
    }

    // Override the getCustomColumns method to tell stancl/tenancy about our custom columns
    public static function getCustomColumns(): array
    {
        return static::$customColumns;
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

    // Custom methods for SA business logic
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return $this->is_verified === true && $this->verified_at !== null;
    }

    public function hasValidSubscription(): bool
    {
        if (!$this->subscription_end_date) {
            return $this->status === 'trial';
        }

        return $this->subscription_end_date->isFuture() && $this->isActive();
    }

    public function getRemainingUsage(string $type): int
    {
        $field = "max_{$type}_per_month";
        $maxAllowed = $this->$field ?? 0;

        // Get current month usage - this would integrate with usage tracking
        $currentUsage = 0; // TODO: Implement usage tracking query

        return max(0, $maxAllowed - $currentUsage);
    }

    public function canSendMessage(string $channel): bool
    {
        if (!$this->isActive() || !$this->hasValidSubscription()) {
            return false;
        }

        $allowedChannels = $this->allowed_channels ?? [];
        return in_array($channel, $allowedChannels);
    }

    // POPIA compliance helpers
    public function hasValidPopiaConsent(): bool
    {
        return $this->popia_consent_obtained && $this->popia_consent_date !== null;
    }

    public function isGdprApplicable(): bool
    {
        return $this->gdpr_applicable === true;
    }

    // SA business validation helpers
    public function getFormattedCompanyNumber(): ?string
    {
        if (!$this->company_registration_number) {
            return null;
        }

        // Format: 2019/123456/07
        return $this->company_registration_number;
    }

    public function getFormattedVatNumber(): ?string
    {
        if (!$this->vat_number) {
            return null;
        }

        // Ensure 10 digits for SA VAT
        return str_pad($this->vat_number, 10, '0', STR_PAD_LEFT);
    }

    protected static function booted(): void
    {
        parent::booted();

        static::saved(function ($tenant) {
            // Clear tenant-specific cache keys
            Cache::forget("tenant_data_{$tenant->id}");
            Cache::forget("document_types_{$tenant->id}");
            Cache::forget("users_query_{$tenant->id}");
            Cache::forget("tenant_divisions_{$tenant->id}");
            Cache::forget("tenant_users_{$tenant->id}");
        });

        static::deleted(function ($tenant) {
            // Clear tenant-specific cache keys
            Cache::forget("tenant_data_{$tenant->id}");
            Cache::forget("document_types_{$tenant->id}");
            Cache::forget("users_query_{$tenant->id}");
            Cache::forget("tenant_divisions_{$tenant->id}");
            Cache::forget("tenant_users_{$tenant->id}");
        });
    }

    public function getCachedDivisions()
    {
        return Cache::remember("tenant_divisions_{$this->id}", 3600, function () {
            return $this->divisions()->get();
        });
    }

    public function getCachedUsers()
    {
        return Cache::remember("tenant_users_{$this->id}", 1800, function () {
            return $this->users()->with('userType')->get();
        });
    }
}
