<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

/**
 * ConsentRecord Model
 *
 * POPIA-compliant consent tracking for all data processing activities.
 * Maintains full audit trail as required by South African data protection law.
 *
 * POPIA Requirements Met:
 * - Voluntary consent (user actively opts in)
 * - Specific consent (unbundled by type)
 * - Informed consent (full text recorded)
 * - Explicit consent (not implied)
 * - Easily withdrawable (withdraw() method)
 * - Audit trail (IP, user agent, timestamps)
 * - 5+ year retention (enforced by policy)
 *
 * @property int $id
 * @property string $tenant_id
 * @property int|null $user_id
 * @property string $consent_type (processing, marketing, profiling, third_party_sharing)
 * @property bool $granted
 * @property string $consent_text
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $consented_at
 * @property \Illuminate\Support\Carbon|null $withdrawn_at
 * @property string|null $withdrawal_reason
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ConsentRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'consent_records';

    /**
     * Consent types as defined by POPIA requirements.
     */
    public const TYPE_PROCESSING = 'processing';           // Mandatory

    public const TYPE_MARKETING = 'marketing';             // Optional

    public const TYPE_PROFILING = 'profiling';             // Optional

    public const TYPE_THIRD_PARTY = 'third_party_sharing'; // Optional

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'consent_type',
        'granted',
        'consent_text',
        'ip_address',
        'user_agent',
        'consented_at',
        'withdrawn_at',
        'withdrawal_reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'tenant_id' => 'string',
            'user_id' => 'integer',
            'granted' => 'boolean',
            'consented_at' => 'timestamp',
            'withdrawn_at' => 'timestamp',
        ];
    }

    /**
     * Boot the model and auto-capture IP and user agent for audit trail.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-assign tenant_id if not set
            if (empty($model->tenant_id) && auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }

            // Auto-capture IP address if not set
            if (empty($model->ip_address)) {
                $model->ip_address = Request::ip();
            }

            // Auto-capture user agent if not set
            if (empty($model->user_agent)) {
                $model->user_agent = Request::userAgent();
            }

            // Set consented_at timestamp if not set
            if (empty($model->consented_at)) {
                $model->consented_at = now();
            }
        });
    }

    /**
     * Get the tenant that owns this consent record.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who gave this consent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this consent is currently active.
     *
     * Active consent means:
     * - Granted is true
     * - Not withdrawn (withdrawn_at is null)
     */
    public function isActive(): bool
    {
        return $this->granted && is_null($this->withdrawn_at);
    }

    /**
     * Withdraw this consent with optional reason.
     *
     * Per POPIA requirements, consent must be easily withdrawable.
     *
     * @param  string|null  $reason  User-provided reason (optional)
     * @return bool Success
     */
    public function withdraw(?string $reason = null): bool
    {
        if (! $this->isActive()) {
            return false; // Already withdrawn
        }

        $this->withdrawn_at = now();
        $this->withdrawal_reason = $reason;
        $this->granted = false;

        return $this->save();
    }

    /**
     * Check if this consent has been withdrawn.
     */
    public function isWithdrawn(): bool
    {
        return ! is_null($this->withdrawn_at);
    }

    /**
     * Get the consent age in days.
     */
    public function getAgeInDays(): int
    {
        return $this->consented_at->diffInDays(now());
    }

    /**
     * Scope query to only active consents.
     */
    public function scopeActive($query)
    {
        return $query->where('granted', true)
            ->whereNull('withdrawn_at');
    }

    /**
     * Scope query to only withdrawn consents.
     */
    public function scopeWithdrawn($query)
    {
        return $query->whereNotNull('withdrawn_at');
    }

    /**
     * Scope query to specific consent type.
     *
     * @param  mixed  $query
     * @param  string  $type  One of: processing, marketing, profiling, third_party_sharing
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('consent_type', $type);
    }

    /**
     * Scope query to processing consent (mandatory).
     */
    public function scopeProcessing($query)
    {
        return $query->byType(self::TYPE_PROCESSING);
    }

    /**
     * Scope query to marketing consent (optional).
     */
    public function scopeMarketing($query)
    {
        return $query->byType(self::TYPE_MARKETING);
    }

    /**
     * Scope query to profiling consent (optional).
     */
    public function scopeProfiling($query)
    {
        return $query->byType(self::TYPE_PROFILING);
    }

    /**
     * Scope query to third party sharing consent (optional).
     */
    public function scopeThirdPartySharing($query)
    {
        return $query->byType(self::TYPE_THIRD_PARTY);
    }

    /**
     * Scope query to consents for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if user has active consent of specific type.
     */
    public static function userHasActiveConsent(int $userId, string $type): bool
    {
        return self::forUser($userId)
            ->byType($type)
            ->active()
            ->exists();
    }

    /**
     * Record new consent for a user.
     *
     * Creates audit trail with IP, user agent, and timestamp.
     *
     * @param  int|null  $userId  User ID (null for pre-registration)
     * @param  string  $type  Consent type
     * @param  string  $consentText  Full text shown to user
     * @param  bool  $granted  Whether consent was granted
     */
    public static function recordConsent(
        ?int $userId,
        string $type,
        string $consentText,
        bool $granted = true
    ): self {
        return self::create([
            'user_id' => $userId,
            'consent_type' => $type,
            'consent_text' => $consentText,
            'granted' => $granted,
            // IP and user agent auto-captured in boot()
        ]);
    }

    /**
     * Get full audit trail for a user.
     *
     * Returns all consent records (active and withdrawn) for compliance reporting.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAuditTrailForUser(int $userId)
    {
        return self::forUser($userId)
            ->orderBy('consented_at', 'desc')
            ->get();
    }

    /**
     * Get consent text templates (can be moved to config/popia.php).
     */
    public static function getConsentText(string $type): string
    {
        $texts = [
            self::TYPE_PROCESSING => 'I hereby consent to Commatix processing my personal information in accordance with the Protection of Personal Information Act (POPIA). I understand that my information will be used solely for the purposes of providing the Commatix service and will be stored securely in South Africa. I have the right to access, correct, or request deletion of my personal information at any time.',

            self::TYPE_MARKETING => "I consent to receiving marketing communications from Commatix, including product updates, promotional offers, and newsletters via email and SMS. I understand that I can withdraw this consent at any time by clicking 'unsubscribe' in any email or replying 'STOP' to SMS messages.",

            self::TYPE_PROFILING => 'I consent to Commatix using automated processing and profiling to personalize my experience, provide recommendations, and improve service delivery. This may include analysis of my usage patterns and preferences.',

            self::TYPE_THIRD_PARTY => 'I consent to Commatix sharing my personal information with trusted third-party service providers (such as payment processors, email service providers, and analytics tools) solely for the purpose of delivering and improving the Commatix service. All third parties are contractually bound to protect my information and comply with POPIA.',
        ];

        return $texts[$type] ?? '';
    }
}
