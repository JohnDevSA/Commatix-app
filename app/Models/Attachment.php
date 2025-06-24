<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'task_milestone_id' => 'integer',
            'document_type_id' => 'integer',
            'required' => 'boolean',
            'uploaded_by' => 'integer',
            'uploaded_at' => 'timestamp',
            'user_id' => 'integer',
        ];
    }

    public function taskMilestone(): BelongsTo
    {
        return $this->belongsTo(TaskMilestone::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
