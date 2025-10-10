<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneDocumentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'milestone_id',
        'document_type_id',
        'attachment_name',
        'is_required',
        'template_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'milestone_id' => 'integer',
            'document_type_id' => 'integer',
            'is_required' => 'boolean',
            'template_id' => 'integer',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
