<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'milestone_id',
        'task_milestone_id',
        'result_name',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'milestone_id' => 'integer',
            'task_milestone_id' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function taskMilestone(): BelongsTo
    {
        return $this->belongsTo(TaskMilestone::class);
    }
}
