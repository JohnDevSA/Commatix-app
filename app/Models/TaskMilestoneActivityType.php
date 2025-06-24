<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskMilestoneActivityType extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'task_milestone_id' => 'integer',
        ];
    }

    public function taskMilestone(): BelongsTo
    {
        return $this->belongsTo(TaskMilestone::class);
    }
}
