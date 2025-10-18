<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OnboardingProgress Model
 *
 * Tracks the progress of a tenant through the 6-step onboarding wizard.
 * This model is tenant-scoped and stores completion status for each step.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $current_step Current wizard step (1-6)
 * @property bool $step_1_completed Company Info step
 * @property bool $step_2_completed User Role & Team step
 * @property bool $step_3_completed Primary Use Case step
 * @property bool $step_4_completed SA Integrations step
 * @property bool $step_5_completed POPIA Consent step
 * @property bool $step_6_completed Pricing Selection step
 * @property array|null $step_data JSON storage for step form data
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $abandoned_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class OnboardingProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'onboarding_progress';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'current_step',
        'step_1_completed',
        'step_2_completed',
        'step_3_completed',
        'step_4_completed',
        'step_5_completed',
        'step_6_completed',
        'step_data',
        'started_at',
        'completed_at',
        'abandoned_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'tenant_id' => 'string',
            'current_step' => 'integer',
            'step_1_completed' => 'boolean',
            'step_2_completed' => 'boolean',
            'step_3_completed' => 'boolean',
            'step_4_completed' => 'boolean',
            'step_5_completed' => 'boolean',
            'step_6_completed' => 'boolean',
            'step_data' => 'array',
            'started_at' => 'timestamp',
            'completed_at' => 'timestamp',
            'abandoned_at' => 'timestamp',
        ];
    }

    /**
     * Boot the model and auto-assign tenant_id
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tenant_id) && auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * Get the tenant that owns this onboarding progress.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if all 6 steps are completed.
     */
    public function isComplete(): bool
    {
        return $this->step_1_completed
            && $this->step_2_completed
            && $this->step_3_completed
            && $this->step_4_completed
            && $this->step_5_completed
            && $this->step_6_completed;
    }

    /**
     * Get the completion percentage (0-100).
     */
    public function getCompletionPercentage(): float
    {
        $completedSteps = collect([
            $this->step_1_completed,
            $this->step_2_completed,
            $this->step_3_completed,
            $this->step_4_completed,
            $this->step_5_completed,
            $this->step_6_completed,
        ])->filter()->count();

        return round(($completedSteps / 6) * 100, 2);
    }

    /**
     * Mark a specific step as completed.
     */
    public function completeStep(int $step): bool
    {
        if ($step < 1 || $step > 6) {
            return false;
        }

        $field = "step_{$step}_completed";
        $this->$field = true;

        // Update current_step to next incomplete step, or 6 if all complete
        $this->current_step = $this->getNextIncompleteStep() ?? 6;

        // If all steps complete, set completed_at timestamp
        if ($this->isComplete()) {
            $this->completed_at = now();
        }

        return $this->save();
    }

    /**
     * Get the next incomplete step number, or null if all complete.
     */
    public function getNextIncompleteStep(): ?int
    {
        for ($i = 1; $i <= 6; $i++) {
            $field = "step_{$i}_completed";
            if (! $this->$field) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Save data for a specific step.
     */
    public function saveStepData(int $step, array $data): bool
    {
        if ($step < 1 || $step > 6) {
            return false;
        }

        $stepData = $this->step_data ?? [];
        $stepData["step_{$step}"] = $data;
        $this->step_data = $stepData;

        return $this->save();
    }

    /**
     * Get data for a specific step.
     */
    public function getStepData(int $step): ?array
    {
        if ($step < 1 || $step > 6) {
            return null;
        }

        return $this->step_data["step_{$step}"] ?? null;
    }

    /**
     * Mark the onboarding as abandoned.
     */
    public function markAbandoned(): bool
    {
        $this->abandoned_at = now();

        return $this->save();
    }

    /**
     * Check if onboarding is abandoned (started but not completed in X days).
     */
    public function isAbandoned(int $days = 7): bool
    {
        if ($this->isComplete()) {
            return false;
        }

        return $this->started_at->diffInDays(now()) > $days;
    }

    /**
     * Scope query to only completed onboarding.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Scope query to only incomplete onboarding.
     */
    public function scopeIncomplete($query)
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Scope query to abandoned onboarding (started > X days ago, not completed).
     */
    public function scopeAbandoned($query, int $days = 7)
    {
        return $query->whereNull('completed_at')
            ->where('started_at', '<', now()->subDays($days));
    }
}
