<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Milestone extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'workflow_template_id' => 'integer',
            'status_id' => 'integer',
            'approval_group_id' => 'integer',
            'requires_docs' => 'boolean',
            'actions' => 'array',
            'status_type_id' => 'integer',
        ];
    }

    public function workflowTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function statusType(): BelongsTo
    {
        return $this->belongsTo(StatusType::class);
    }

    public function milestoneActivityTypes(): BelongsToMany
    {
        return $this->belongsToMany(MilestoneActivityType::class);
    }
}
