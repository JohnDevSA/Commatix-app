<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessScope extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function documentTypes(): HasMany
    {
        return $this->hasMany(DocumentType::class);
    }

    public function workflowTemplates(): HasMany
    {
        return $this->hasMany(WorkflowTemplate::class);
    }
}
