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

    // DEPRECATED: These methods have been moved to TaskProgressionService
    // Use TaskProgressionService instead for better separation of concerns
    /**
     * @deprecated Use TaskProgressionService::canStartEarly() instead
     */
    public function canStartEarly(): bool
    {
        // Implementation moved to TaskProgressionService
        return false;
    }

    /**
     * @deprecated Use TaskProgressionService::shouldAutoStart() instead
     */
    public function shouldAutoStart(): bool
    {
        // Implementation moved to TaskProgressionService
        return false;
    }

    /**
     * @deprecated Use TaskProgressionService::start() instead
     */
    public function startTask(?string $reason = null): bool
    {
        // Implementation moved to TaskProgressionService
        return false;
    }

    /**
     * @deprecated Use TaskProgressionService::moveToNext() instead
     */
    public function moveToNextMilestone(): bool
    {
        // Implementation moved to TaskProgressionService
        return false;
    }
}