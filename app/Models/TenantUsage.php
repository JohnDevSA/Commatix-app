<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantUsage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tenant_id' => 'integer',
            'period_start' => 'timestamp',
            'period_end' => 'timestamp',
            'storage_used_mb' => 'decimal',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
