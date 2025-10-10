<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Check if task can start early (before scheduled date).
     *
     * This method delegates to TaskProgressionService for business logic.
     */
    public function canStartEarly(): bool
    {
        return $this->status === 'scheduled'
            && $this->scheduled_start_date
            && Carbon::parse($this->scheduled_start_date)->isFuture();
    }

    /**
     * Check if task should auto-start based on scheduled date.
     *
     * This method delegates to TaskProgressionService for business logic.
     */
    public function shouldAutoStart(): bool
    {
        return $this->status === 'scheduled'
            && $this->scheduled_start_date
            && Carbon::parse($this->scheduled_start_date)->isToday();
    }

    /**
     * Start the task.
     *
     * Delegates to TaskProgressionService for complex business logic.
     *
     * @param  string|null  $reason  Optional reason for early start
     */
    public function startTask(?string $reason = null): bool
    {
        return app(\App\Contracts\Services\TaskProgressionInterface::class)
            ->startTask($this, $reason);
    }

    /**
     * Move task to next milestone.
     *
     * Delegates to TaskProgressionService for complex business logic.
     */
    public function moveToNextMilestone(): bool
    {
        try {
            $result = app(\App\Contracts\Services\TaskProgressionInterface::class)
                ->progressToNextMilestone($this, auth()->user());

            return $result !== null || $this->status === 'completed';
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to move task {$this->id} to next milestone: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Get task completion percentage.
     */
    public function getCompletionPercentage(): float
    {
        return app(\App\Contracts\Services\TaskProgressionInterface::class)
            ->getCompletionPercentage($this);
    }

    /**
     * Check if task can progress to next milestone.
     */
    public function canProgress(): bool
    {
        return app(\App\Contracts\Services\TaskProgressionInterface::class)
            ->canProgress($this);
    }
}
