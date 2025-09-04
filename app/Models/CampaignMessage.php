<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignMessage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'campaign_id' => 'integer',
            'subscriber_id' => 'integer',
            'sent_at' => 'timestamp',
            'delivered_at' => 'timestamp',
            'opened_at' => 'timestamp',
            'clicked_at' => 'timestamp',
            'provider_response' => 'array',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}
