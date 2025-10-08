<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantTopUp extends Model
{
    protected $fillable = [
        'tenant_id',
        'channel',
        'amount',
        'reason',
        'added_by',
    ];

    protected $casts = [
        'amount' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the top-up
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who added the top-up
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Scope for specific channel
     */
    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope for current period (this month)
     */
    public function scopeCurrentPeriod($query)
    {
        return $query->where('created_at', '>=', now()->startOfMonth());
    }
}