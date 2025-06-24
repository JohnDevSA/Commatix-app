<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusType extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function workflowTemplates(): HasMany
    {
        return $this->hasMany(WorkflowTemplate::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function taskMilestones(): HasMany
    {
        return $this->hasMany(TaskMilestone::class);
    }

    public function statusScopes(): BelongsToMany
    {
        return $this->belongsToMany(StatusScope::class);
    }
}
