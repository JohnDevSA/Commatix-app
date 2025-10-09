<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class UserType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_super_admin',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_super_admin' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected static function booted(): void
    {
        static::saved(function ($userType) {
            Cache::forget('user_types');
        });

        static::deleted(function ($userType) {
            Cache::forget('user_types');
        });
    }
}