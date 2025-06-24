<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'status_id' => 'integer',
            'status_type_id' => 'integer',
        ];
    }

    public function statusType(): BelongsTo
    {
        return $this->belongsTo(StatusType::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }
}
