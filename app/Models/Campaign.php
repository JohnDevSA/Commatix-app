<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tenant_id' => 'integer',
            'subscriber_list_id' => 'integer',
            'message_template_id' => 'integer',
            'scheduled_at' => 'timestamp',
            'started_at' => 'timestamp',
            'completed_at' => 'timestamp',
            'created_by' => 'integer',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'user_id' => 'integer',
        ];
    }

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaignMessages(): HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }
}
