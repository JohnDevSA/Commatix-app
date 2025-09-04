<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataConsentRecord extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tenant_id' => 'integer',
            'subscriber_id' => 'integer',
            'consent_given' => 'boolean',
            'consent_date' => 'timestamp',
            'withdrawn_at' => 'timestamp',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}
