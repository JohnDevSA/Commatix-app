<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'access_scope_id',
        'tenant_id',
        'industry_category',
        'is_required',
        'allows_multiple',
        'max_file_size_mb',
        'allowed_file_types',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'access_scope_id' => 'integer',
            'tenant_id' => 'string', // UUID for tenant_id
            'is_required' => 'boolean',
            'allows_multiple' => 'boolean',
            'max_file_size_mb' => 'integer',
            'allowed_file_types' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function accessScope(): BelongsTo
    {
        return $this->belongsTo(AccessScope::class);
    }

    // Scope for global/system document types
    public function scopeGlobal($query)
    {
        return $query->whereNull('tenant_id');
    }

    // Scope for industry-specific templates
    public function scopeIndustryTemplate($query, $industry = null)
    {
        $query = $query->whereHas('accessScope', function ($q) {
            $q->where('name', 'industry_template');
        });

        if ($industry) {
            $query->where('industry_category', $industry);
        }

        return $query;
    }

    // Scope for tenant-specific document types
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // Check if this is a global/system document type
    public function isGlobal(): bool
    {
        return is_null($this->tenant_id) &&
            in_array($this->accessScope->name ?? '', ['global', 'industry_template']);
    }

    // Check if this is an industry template
    public function isIndustryTemplate(): bool
    {
        return $this->accessScope && $this->accessScope->name === 'industry_template';
    }

    /**
     * Get documents available to a specific user based on their context
     */
    public static function getAvailableDocuments($user = null)
    {
        $user = $user ?: auth()->user();
        if (!$user) {
            return static::query()->whereRaw('1 = 0'); // Empty query
        }

        $query = static::query();

        // Always include tenant-specific documents for this user's tenant
        if ($user->tenant_id) {
            $query->orWhere('tenant_id', $user->tenant_id);
        }

        // Include global system documents
        $query->orWhere(function ($q) {
            $q->whereNull('tenant_id')
                ->whereHas('accessScope', function ($scope) {
                    $scope->where('name', 'global');
                });
        });

        // Include industry-specific documents based on tenant's industry
        if ($user->tenant && $user->tenant->industry_classification) {
            $query->orWhere(function ($q) use ($user) {
                $q->whereNull('tenant_id')
                    ->whereHas('accessScope', function ($scope) {
                        $scope->where('name', 'industry_template');
                    })
                    ->where(function ($industryQuery) use ($user) {
                        $industryQuery->where('industry_category', $user->tenant->industry_classification)
                            ->orWhere('industry_category', 'general');
                    });
            });
        }

        return $query->distinct();
    }


    /**
     * Get documents available for a specific division
     */
    public static function getDocumentsForDivision($divisionId = null, $user = null)
    {
        $user = $user ?: auth()->user();
        $query = static::getAvailableDocuments($user);

        // If user has a division, we could filter by division-specific documents
        // For now, we'll use the base available documents
        // This can be expanded if you add division-specific document relationships

        return $query;
    }

    /**
     * Get documents for workflow creation - prioritizes division-specific, then industry, then global
     */
    public static function getWorkflowDocuments($user = null)
    {
        $user = $user ?: auth()->user();

        // Start with division-specific documents if user has a division
        if ($user && $user->division_id) {
            return static::getDocumentsForDivision($user->division_id, $user);
        }

        // Fall back to general available documents
        return static::getAvailableDocuments($user);
    }



    /**
     * Get document types as options for form selects in workflows
     */
    public static function getWorkflowDocumentOptions($user = null): array
    {
        return static::getWorkflowDocuments($user)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Get document types grouped by access scope for better organization
     */
    public static function getGroupedWorkflowDocuments($user = null): array
    {
        $documents = static::getWorkflowDocuments($user)
            ->with('accessScope')
            ->orderBy('name')
            ->get();

        return $documents->groupBy(function ($doc) {
            if ($doc->tenant_id) {
                return 'Tenant Specific';
            } elseif ($doc->accessScope && $doc->accessScope->name === 'industry_template') {
                return 'Industry Templates';
            } elseif ($doc->accessScope && $doc->accessScope->name === 'global') {
                return 'Global System';
            }
            return 'Other';
        })->map(function ($group) {
            return $group->pluck('name', 'id')->toArray();
        })->toArray();
    }
}
