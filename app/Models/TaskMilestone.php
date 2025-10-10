<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'milestone_id',
        'status_id',
        'sla_days',
        'sla_hours',
        'sla_minutes',
        'approval_group_id',
        'approval_group_name',
        'requires_docs',
        'actions',
        'completed_at',
        'status_type_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'milestone_id' => 'integer',
            'status_id' => 'integer',
            'approval_group_id' => 'integer',
            'requires_docs' => 'boolean',
            'actions' => 'array',
            'completed_at' => 'timestamp',
            'status_type_id' => 'integer',
            'sla_days' => 'integer',
            'sla_hours' => 'integer',
            'sla_minutes' => 'integer',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function statusType(): BelongsTo
    {
        return $this->belongsTo(StatusType::class);
    }

    public function taskMilestoneActivityTypes(): HasMany
    {
        return $this->hasMany(TaskMilestoneActivityType::class);
    }

    public function milestoneResults(): HasMany
    {
        return $this->hasMany(MilestoneResult::class);
    }
}
