<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        return $this->userType?->name === 'Tenant Admin';
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
            Cache::userCache($user->id)->flush();
            Cache::tenantCache($user->tenant_id)->flush();
        });

        static::deleted(function ($user) {
            Cache::userCache($user->id)->flush();
            Cache::tenantCache($user->tenant_id)->flush();
        });
    }
}
