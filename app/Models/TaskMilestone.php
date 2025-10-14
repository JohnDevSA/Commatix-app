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
        'task_id',
        'milestone_id',
        'sequence_order',
        'status',
        'status_id',
        'sla_days',
        'sla_hours',
        'sla_minutes',
        'approval_group_id',
        'approval_group_name',
        'requires_docs',
        'actions',
        'completed_at',
        'started_at',
        'completed_by',
        'completion_notes',
        'status_type_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'task_id' => 'integer',
            'milestone_id' => 'integer',
            'sequence_order' => 'integer',
            'status_id' => 'integer',
            'approval_group_id' => 'integer',
            'requires_docs' => 'boolean',
            'actions' => 'array',
            'completed_at' => 'timestamp',
            'started_at' => 'timestamp',
            'completed_by' => 'integer',
            'status_type_id' => 'integer',
            'sla_days' => 'integer',
            'sla_hours' => 'integer',
            'sla_minutes' => 'integer',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function statusType(): BelongsTo
    {
        return $this->belongsTo(StatusType::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
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
