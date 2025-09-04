<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tenant_id' => 'integer',
            'subscriber_list_id' => 'integer',
            'opt_out_date' => 'timestamp',
            'tags' => 'array',
            'custom_fields' => 'array',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
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

    public function campaignMessages(): HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }

    public function dataConsentRecords(): HasMany
    {
        return $this->hasMany(DataConsentRecord::class);
    }
}
