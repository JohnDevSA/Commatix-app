<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        return $this->belongsTo(ParentTemplate::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function childTemplates(): HasMany
    {
        return $this->hasMany(ChildTemplates::class);
    }

    public function workflowTemplateUsages(): HasMany
    {
        return $this->hasMany(WorkflowTemplateUsage::class);
    }

    public function workflowTemplateTags(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowTemplateTag::class);
    }
}
