<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_template_id',
        'name',
        'description',
        'sequence_order',
        'estimated_duration_days',
        'milestone_type',
        'priority',
        'status_id',
        'status_type_id',
        'hint',
        'sla_days',
        'sla_hours',
        'sla_minutes',
        'approval_group_id',
        'approval_group_name',
        'requires_docs',
        'requires_approval',
        'can_be_skipped',
        'auto_complete',
        'completion_criteria',
        'actions',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'workflow_template_id' => 'integer',
            'status_id' => 'integer',
            'approval_group_id' => 'integer',
            'requires_docs' => 'boolean',
            'requires_approval' => 'boolean',
            'can_be_skipped' => 'boolean',
            'auto_complete' => 'boolean',
            'actions' => 'array',
            'status_type_id' => 'integer',
            'sla_days' => 'integer',
            'sla_hours' => 'integer',
            'sla_minutes' => 'integer',
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

    public function documentRequirements(): BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'milestone_document_requirements')
            ->withPivot('is_required', 'allows_multiple', 'sequence_order', 'instructions', 'validation_rules')
            ->withTimestamps()
            ->orderBy('milestone_document_requirements.sequence_order');
    }

    public function requiredDocuments(): BelongsToMany
    {
        return $this->documentRequirements()->wherePivot('is_required', true);
    }

    public function optionalDocuments(): BelongsToMany
    {
        return $this->documentRequirements()->wherePivot('is_required', false);
    }

    public function milestoneResults(): HasMany
    {
        return $this->hasMany(MilestoneResult::class);
    }

    public function documentAttachments(): HasMany
    {
        return $this->hasMany(MilestoneDocumentAttachment::class);
    }

    // Helper methods for beautiful UI
    public function getDocumentRequirementsList(): array
    {
        return $this->documentRequirements->map(function ($doc) {
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'description' => $doc->description,
                'is_required' => $doc->pivot->is_required,
                'allows_multiple' => $doc->pivot->allows_multiple,
                'instructions' => $doc->pivot->instructions,
                'validation_rules' => $doc->pivot->validation_rules,
                'sequence_order' => $doc->pivot->sequence_order,
            ];
        })->toArray();
    }

    public function getRequiredDocumentsCount(): int
    {
        return $this->requiredDocuments()->count();
    }

    public function getOptionalDocumentsCount(): int
    {
        return $this->optionalDocuments()->count();
    }
}