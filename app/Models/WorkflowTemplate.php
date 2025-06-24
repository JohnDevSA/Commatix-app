<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowTemplate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'division_id' => 'integer',
            'status_id' => 'integer',
            'access_scope_id' => 'integer',
            'status_type_id' => 'integer',
        ];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function statusType(): BelongsTo
    {
        return $this->belongsTo(StatusType::class);
    }

    public function accessScope(): BelongsTo
    {
        return $this->belongsTo(AccessScope::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }
}
