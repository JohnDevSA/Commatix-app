<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkflowTemplateTag extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function workflowTemplates(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowTemplate::class);
    }
}
