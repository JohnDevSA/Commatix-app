<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tenant_id',
        'channel',
        'subject',
        'content',
        'variables',
        'is_active',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tenant_id) && tenant()) {
                $model->tenant_id = tenant()->id;
            }
            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tenant_id' => 'string',
            'variables' => 'array',
            'is_active' => 'boolean',
            'created_by' => 'integer',
        ];
    }

    // Relationships

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    // Helper methods

    public function isEmail(): bool
    {
        return $this->channel === 'email';
    }

    public function isSms(): bool
    {
        return $this->channel === 'sms';
    }

    public function isWhatsApp(): bool
    {
        return $this->channel === 'whatsapp';
    }

    public function isVoice(): bool
    {
        return $this->channel === 'voice';
    }

    public function needsSubject(): bool
    {
        return $this->isEmail();
    }

    /**
     * Get character count for SMS (useful for multi-part SMS calculation)
     */
    public function getContentLength(): int
    {
        return mb_strlen($this->content);
    }

    /**
     * Estimate SMS parts (160 chars per part for GSM, 70 for Unicode)
     */
    public function estimateSMSParts(): int
    {
        if (! $this->isSms()) {
            return 0;
        }

        $length = $this->getContentLength();
        $isUnicode = preg_match('/[^\x20-\x7E]/', $this->content);

        $limit = $isUnicode ? 70 : 160;

        return (int) ceil($length / $limit);
    }
}
