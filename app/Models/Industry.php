<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'color',
        'sic_codes',
        'typical_compliance_requirements',
        'requires_fica',
        'requires_bee_compliance',
        'typical_workflow_duration_days',
        'common_document_types',
        'regulatory_bodies',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'sic_codes' => 'array',
        'typical_compliance_requirements' => 'array',
        'requires_fica' => 'boolean',
        'requires_bee_compliance' => 'boolean',
        'common_document_types' => 'array',
        'regulatory_bodies' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'typical_workflow_duration_days' => 'integer',
    ];

    // Relationships
    public function documentTypes(): HasMany
    {
        return $this->hasMany(DocumentType::class, 'industry_category', 'code');
    }

    public function workflowTemplates(): HasMany
    {
        return $this->hasMany(WorkflowTemplate::class, 'industry_category', 'code');
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'industry_classification', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper methods
    public function getDisplayNameAttribute(): string
    {
        return $this->icon ? $this->icon.' '.$this->name : $this->name;
    }

    public static function getSelectOptions(): array
    {
        return static::active()->ordered()->pluck('name', 'code')->toArray();
    }

    public static function getDisplayOptions(): array
    {
        return static::active()->ordered()->get()->mapWithKeys(function ($industry) {
            return [$industry->code => $industry->display_name];
        })->toArray();
    }
}
