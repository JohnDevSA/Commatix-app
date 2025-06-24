<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneActivity extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'milestone_id' => 'integer',
            'milestone_activity_type_id' => 'integer',
            'user_id' => 'integer',
            'metadata' => 'array',
            'created_at' => 'timestamp',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestoneActivityType(): BelongsTo
    {
        return $this->belongsTo(MilestoneActivityType::class);
    }
}
