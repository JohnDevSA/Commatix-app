<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class WorkflowTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'workflow_code',
        'name',
        'description',
        'customization_notes',
        'email_enabled',
        'sms_enabled',
        'whatsapp_enabled',
        'voice_enabled',
        'access_scope_id',
        'tenant_id',
        'division_id',
        'template_type',
        'parent_template_id',
        'industry_category',
        'category',
        'industry',
        'template_version',
        'channels',
        'steps',
        'status_id',
        'status_type_id',
        'created_by',
        'user_id',
        'is_public',
        'is_system_template',
        'usage_count',
        'last_used_at',
        'tags',
        'estimated_duration_days',
        'complexity_level',
        'is_customizable',
        'locked_milestones',
        'required_roles',
        'is_published',
        'is_active',
        'published_at',
        'change_log',
        'is_locked',
        'locked_by_user_id',
        'locked_at',
        'lock_reason',
        'milestones_completed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->workflow_code)) {
                $model->workflow_code = 'WF-' . strtoupper(Str::random(8));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'division_id' => 'integer',
            'status_id' => 'integer',
            'access_scope_id' => 'integer',
            'parent_template_id' => 'integer',
            'created_by' => 'integer',
            'is_public' => 'boolean',
            'is_system_template' => 'boolean',
            'last_used_at' => 'timestamp',
            'tags' => 'array',
            'is_customizable' => 'boolean',
            'locked_milestones' => 'array',
            'required_roles' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'timestamp',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'status_type_id' => 'integer',
            'user_id' => 'integer',
            'is_locked' => 'boolean',
            'locked_by_user_id' => 'integer',
            'locked_at' => 'timestamp',
            'milestones_completed' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'parent_template_id');
    }

    public function childTemplates(): HasMany
    {
        return $this->hasMany(WorkflowTemplate::class, 'parent_template_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function workflowTemplateUsages(): HasMany
    {
        return $this->hasMany(WorkflowTemplateUsage::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowTemplateTag::class);
    }

    public function lockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by_user_id');
    }
    
    // DEPRECATED: These methods have been moved to WorkflowLockService
    // Use WorkflowLockService instead for better separation of concerns
    /**
     * @deprecated Use WorkflowLockService::lock() instead
     */
    public function lockWorkflow(User $user, string $reason = 'Configuring milestones'): void
    {
        // Implementation moved to WorkflowLockService
    }

    /**
     * @deprecated Use WorkflowLockService::unlock() instead
     */
    public function unlockWorkflow(): void
    {
        // Implementation moved to WorkflowLockService
    }

    /**
     * @deprecated Use WorkflowLockService::isLockedBy() instead
     */
    public function isLockedBy(User $user): bool
    {
        // Implementation moved to WorkflowLockService
        return false;
    }

    /**
     * @deprecated Use WorkflowLockService::canBeEditedBy() instead
     */
    public function canBeEditedBy(User $user): bool
    {
        // Implementation moved to WorkflowLockService
        return true;
    }

    /**
     * @deprecated Use WorkflowMilestoneService::markMilestonesComplete() instead
     */
    public function markMilestonesComplete(): void
    {
        // Implementation moved to WorkflowMilestoneService
    }
}