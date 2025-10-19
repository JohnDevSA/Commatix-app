<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Province Model
 *
 * Manages South African provinces with Redis caching for performance.
 * This is a central (non-tenant) model used across all tenants.
 *
 * @property int $id
 * @property string $code Two-letter province code (e.g., 'GP', 'WC')
 * @property string $name Full province name (e.g., 'Gauteng', 'Western Cape')
 * @property array|null $major_cities List of major cities in the province
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Province extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'sa_provinces';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'major_cities',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'major_cities' => 'array',
        ];
    }

    /**
     * Cache key for all provinces
     */
    const CACHE_KEY_ALL = 'provinces:all';

    /**
     * Cache key pattern for province by code
     */
    const CACHE_KEY_BY_CODE = 'provinces:code:%s';

    /**
     * Cache TTL in seconds (24 hours)
     */
    const CACHE_TTL = 86400;

    /**
     * Get all provinces with Redis caching
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllCached()
    {
        try {
            return Cache::remember(self::CACHE_KEY_ALL, self::CACHE_TTL, function () {
                return static::orderBy('name')->get();
            });
        } catch (\Exception $e) {
            \Log::warning('Province cache failed, using direct query', ['error' => $e->getMessage()]);
            return static::orderBy('name')->get();
        }
    }

    /**
     * Get provinces as select options (code => name) with caching
     *
     * @return array
     */
    public static function getSelectOptions(): array
    {
        try {
            return Cache::remember(self::CACHE_KEY_ALL . ':options', self::CACHE_TTL, function () {
                return static::orderBy('name')->pluck('name', 'code')->toArray();
            });
        } catch (\Exception $e) {
            \Log::warning('Province select options cache failed', ['error' => $e->getMessage()]);
            return static::orderBy('name')->pluck('name', 'code')->toArray();
        }
    }

    /**
     * Find province by code with Redis caching
     *
     * @param string $code
     * @return self|null
     */
    public static function findByCodeCached(string $code): ?self
    {
        $cacheKey = sprintf(self::CACHE_KEY_BY_CODE, $code);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($code) {
            return static::where('code', $code)->first();
        });
    }

    /**
     * Clear all province caches
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
        Cache::forget(self::CACHE_KEY_ALL . ':options');

        // Clear individual province caches
        $provinces = static::all();
        foreach ($provinces as $province) {
            $cacheKey = sprintf(self::CACHE_KEY_BY_CODE, $province->code);
            Cache::forget($cacheKey);
        }
    }

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        // Clear cache when provinces are created, updated, or deleted
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Scope to get by code
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
