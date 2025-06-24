<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentType extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'access_scope_id' => 'integer',
            'tenant_id' => 'integer',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function accessScope(): BelongsTo
    {
        return $this->belongsTo(AccessScope::class);
    }
}
