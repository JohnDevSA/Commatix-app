<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tenant_id',
        'message_template_id',
        'subscriber_list_id',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'failed_count',
        'opened_count',
        'clicked_count',
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
            'subscriber_list_id' => 'integer',
            'message_template_id' => 'integer',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'created_by' => 'integer',
            'total_recipients' => 'integer',
            'sent_count' => 'integer',
            'delivered_count' => 'integer',
            'failed_count' => 'integer',
            'opened_count' => 'integer',
            'clicked_count' => 'integer',
        ];
    }

    // Relationships

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscriberList(): BelongsTo
    {
        return $this->belongsTo(SubscriberList::class);
    }

    public function messageTemplate(): BelongsTo
    {
        return $this->belongsTo(MessageTemplate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaignMessages(): HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }

    // Scopes

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSending($query)
    {
        return $query->where('status', 'sending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper methods

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft', 'scheduled', 'paused']);
    }

    public function getSuccessRate(): float
    {
        if ($this->sent_count === 0) {
            return 0.0;
        }

        return round(($this->delivered_count / $this->sent_count) * 100, 2);
    }

    public function getOpenRate(): float
    {
        if ($this->delivered_count === 0) {
            return 0.0;
        }

        return round(($this->opened_count / $this->delivered_count) * 100, 2);
    }

    public function getClickRate(): float
    {
        if ($this->opened_count === 0) {
            return 0.0;
        }

        return round(($this->clicked_count / $this->opened_count) * 100, 2);
    }
}
