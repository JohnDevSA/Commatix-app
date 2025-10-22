<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'error_message',
        'provider_message_id',
        'provider_response',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'campaign_id' => 'integer',
            'subscriber_id' => 'integer',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'opened_at' => 'datetime',
            'clicked_at' => 'datetime',
            'provider_response' => 'array',
        ];
    }

    // Relationships

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Helper methods

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSent(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'opened', 'clicked']);
    }

    public function isDelivered(): bool
    {
        return in_array($this->status, ['delivered', 'opened', 'clicked']);
    }

    public function isOpened(): bool
    {
        return in_array($this->status, ['opened', 'clicked']);
    }

    public function isClicked(): bool
    {
        return $this->status === 'clicked';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'bounced']);
    }

    public function markAsSent(string $providerMessageId, ?array $providerResponse = null): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'provider_message_id' => $providerMessageId,
            'provider_response' => $providerResponse,
        ]);

        // Update campaign stats
        $this->campaign->increment('sent_count');
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        // Update campaign stats
        $this->campaign->increment('delivered_count');
    }

    public function markAsOpened(): void
    {
        if (! $this->isOpened()) {
            $this->update([
                'status' => 'opened',
                'opened_at' => now(),
            ]);

            // Update campaign stats (only count first open)
            $this->campaign->increment('opened_count');
        }
    }

    public function markAsClicked(): void
    {
        $wasNotClicked = ! $this->isClicked();

        $this->update([
            'status' => 'clicked',
            'clicked_at' => now(),
        ]);

        // Update campaign stats (only count first click)
        if ($wasNotClicked) {
            $this->campaign->increment('clicked_count');
        }

        // Also mark as opened if not already
        if (! $this->opened_at) {
            $this->markAsOpened();
        }
    }

    public function markAsFailed(string $errorMessage, ?array $providerResponse = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'provider_response' => $providerResponse,
        ]);

        // Update campaign stats
        $this->campaign->increment('failed_count');
    }
}
