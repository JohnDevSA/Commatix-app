<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'subscriber_list_id',
        'opt_in_date',
        'opt_out_date',
        'status',
        'tags',
        'custom_fields',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tenant_id) && tenant()) {
                $model->tenant_id = tenant()->id;
            } else {
                echo 'Tenant ID is empty';
            }
        });
    }

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

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
