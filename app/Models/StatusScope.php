<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StatusScope extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function statusTypes(): BelongsToMany
    {
        return $this->belongsToMany(StatusType::class);
    }
}
