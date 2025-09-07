<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'workflow_template_id',
        'subscriber_id',
        'tenant_id',
        'division_id',
        'created_by',
        'assigned_to',
        'status',
        'priority',
        'scheduled_start_date',
        'actual_start_date',
        'due_date',
        'completed_at',
        'current_milestone_id',
        'early_start_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'workflow_template_id' => 'integer',
            'subscriber_id' => 'integer',
            'tenant_id' => 'string',
            'division_id' => 'integer',
            'created_by' => 'integer',
            'assigned_to' => 'integer',
            'current_milestone_id' => 'integer',
            'scheduled_start_date' => 'date',
            'actual_start_date' => 'datetime',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function workflowTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function currentMilestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class, 'current_milestone_id');
    }

    public function taskMilestones(): HasMany
    {
        return $this->hasMany(TaskMilestone::class);
    }

    // Business Logic Methods
    public function canStartEarly(): bool
    {
        return $this->status === 'scheduled' &&
            $this->scheduled_start_date > now()->toDateString();
    }

    public function shouldAutoStart(): bool
    {
        return $this->status === 'scheduled' &&
            $this->scheduled_start_date <= now()->toDateString();
    }

    public function startTask(?string $reason = null): bool
    {
        if ($this->status !== 'scheduled') {
            return false;
        }

        $firstMilestone = $this->workflowTemplate->milestones()
            ->orderBy('sequence_order')
            ->first();

        if (!$firstMilestone) {
            return false;
        }

        $this->update([
            'status' => 'in_progress',
            'actual_start_date' => now(),
            'current_milestone_id' => $firstMilestone->id,
            'early_start_reason' => $reason,
        ]);

        // Create task milestone record
        $this->taskMilestones()->create([
            'milestone_id' => $firstMilestone->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return true;
    }

    public function moveToNextMilestone(): bool
    {
        $currentMilestone = $this->currentMilestone;
        if (!$currentMilestone) {
            return false;
        }

        $nextMilestone = $this->workflowTemplate->milestones()
            ->where('sequence_order', '>', $currentMilestone->sequence_order)
            ->orderBy('sequence_order')
            ->first();

        if (!$nextMilestone) {
            // No next milestone, complete the task
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
                'current_milestone_id' => null,
            ]);
        } else {
            $this->update([
                'current_milestone_id' => $nextMilestone->id,
            ]);

            $this->taskMilestones()->create([
                'milestone_id' => $nextMilestone->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        return true;
    }
}
