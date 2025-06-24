<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
