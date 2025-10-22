<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * Industry Model
 *
 * Manages industry classifications with Redis caching for performance.
 * This is a central (non-tenant) model used across all tenants.
 *
 * @property int $id
 * @property string $name Industry name
 * @property string $code Unique industry code
 * @property string|null $description Industry description
 * @property string|null $icon Emoji or icon
 * @property string|null $color Hex color code
 * @property array|null $sic_codes SIC classification codes
 * @property array|null $typical_compliance_requirements
 * @property bool $requires_fica
 * @property bool $requires_bee_compliance
 * @property int|null $typical_workflow_duration_days
 * @property array|null $common_document_types
 * @property array|null $regulatory_bodies
 * @property bool $is_active
 * @property int $sort_order
 */
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

    /**
     * Cache key for all active industries
     */
    const CACHE_KEY_ALL_ACTIVE = 'industries:active:all';

    /**
     * Cache key for industry select options
     */
    const CACHE_KEY_SELECT_OPTIONS = 'industries:active:options';

    /**
     * Cache key pattern for industry by ID
     */
    const CACHE_KEY_BY_ID = 'industries:id:%s';

    /**
     * Cache key pattern for industry by code
     */
    const CACHE_KEY_BY_CODE = 'industries:code:%s';

    /**
     * Cache TTL in seconds (24 hours)
     */
    const CACHE_TTL = 86400;

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

    /**
     * Get all active industries with Redis caching
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllActiveCached()
    {
        try {
            return Cache::remember(self::CACHE_KEY_ALL_ACTIVE, self::CACHE_TTL, function () {
                return static::active()->ordered()->get();
            });
        } catch (\Exception $e) {
            // If cache fails (Redis down), query database directly
            \Log::warning('Industry cache failed, using direct query', ['error' => $e->getMessage()]);

            return static::active()->ordered()->get();
        }
    }

    /**
     * Get industries as select options (ID => name) with Redis caching
     */
    public static function getSelectOptions(): array
    {
        try {
            return Cache::remember(self::CACHE_KEY_SELECT_OPTIONS, self::CACHE_TTL, function () {
                return static::active()->ordered()->pluck('name', 'id')->toArray();
            });
        } catch (\Exception $e) {
            \Log::warning('Industry select options cache failed', ['error' => $e->getMessage()]);

            return static::active()->ordered()->pluck('name', 'id')->toArray();
        }
    }

    /**
     * Get industries with display names (code => display_name)
     */
    public static function getDisplayOptions(): array
    {
        return Cache::remember(self::CACHE_KEY_SELECT_OPTIONS.':display', self::CACHE_TTL, function () {
            return static::active()->ordered()->get()->mapWithKeys(function ($industry) {
                return [$industry->code => $industry->display_name];
            })->toArray();
        });
    }

    /**
     * Find industry by ID with Redis caching
     */
    public static function findCached(int $id): ?self
    {
        $cacheKey = sprintf(self::CACHE_KEY_BY_ID, $id);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Find industry by code with Redis caching
     */
    public static function findByCodeCached(string $code): ?self
    {
        $cacheKey = sprintf(self::CACHE_KEY_BY_CODE, $code);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($code) {
            return static::where('code', $code)->first();
        });
    }

    /**
     * Clear all industry caches
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL_ACTIVE);
        Cache::forget(self::CACHE_KEY_SELECT_OPTIONS);
        Cache::forget(self::CACHE_KEY_SELECT_OPTIONS.':display');

        // Clear individual industry caches
        $industries = static::all();
        foreach ($industries as $industry) {
            Cache::forget(sprintf(self::CACHE_KEY_BY_ID, $industry->id));
            if ($industry->code) {
                Cache::forget(sprintf(self::CACHE_KEY_BY_CODE, $industry->code));
            }
        }
    }

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        // Clear cache when industries are created, updated, or deleted
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}
