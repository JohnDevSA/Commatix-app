<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use HasFactory;
    use Impersonate;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type_id',
        'tenant_id',
        'division_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_type_id' => 'integer',
            'tenant_id' => 'string',
            'email_verified_at' => 'timestamp',
            'division_id' => 'integer',
        ];
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    // Helper methods
    public function isSuperAdmin(): bool
    {
        return $this->userType?->name === 'Super Admin';
    }

    public function isTenantAdmin(): bool
    {
        return $this->userType?->name === 'Admin';
    }

    public function canAccessTenant(string $tenantId): bool
    {
        return $this->isSuperAdmin() || $this->tenant_id === $tenantId;
    }

    public function hasGlobalAccess(): bool
    {
        return $this->isSuperAdmin() && $this->tenant_id === null;
    }

    protected static function booted(): void
    {
        static::saved(function ($user) {
            Cache::forget('user_' . $user->id);
            Cache::forget('tenant_users_' . $user->tenant_id);
        });

        static::deleted(function ($user) {
            Cache::forget('user_' . $user->id);
            Cache::forget('tenant_users_' . $user->tenant_id);
        });
    }

    /**
     * Determine if the user can impersonate another user.
     */
    public function canImpersonate(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Determine if the user can be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        // Super admins cannot be impersonated
        return ! $this->isSuperAdmin();
    }

    // Authorization Helper Methods

    /**
     * Check if user can access global/system-wide resources
     */
    public function canAccessGlobalResources(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Check if user can manage their tenant
     */
    public function canManageTenant(): bool
    {
        return $this->isTenantAdmin() || $this->isSuperAdmin();
    }

    /**
     * Check if user can view a specific tenant's data
     */
    public function canViewTenantData(string $tenantId): bool
    {
        return $this->isSuperAdmin() || $this->tenant_id === $tenantId;
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin() || $this->isTenantAdmin();
    }

    /**
     * Check if user can manage workflows
     */
    public function canManageWorkflows(): bool
    {
        return $this->isSuperAdmin() || $this->isTenantAdmin();
    }

    /**
     * Check if user can view system administration
     */
    public function canAccessSystemAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Get the query scope for tenant-based resources
     */
    public function getTenantScopeQuery(Builder $query): Builder
    {
        if ($this->isSuperAdmin()) {
            return $query; // Super admins see everything
        }

        return $query->where('tenant_id', $this->tenant_id);
    }
}
