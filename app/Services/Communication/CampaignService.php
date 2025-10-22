<?php

namespace App\Services\Communication;

use App\Contracts\Services\CampaignServiceInterface;
use App\Jobs\MonitorCampaignProgressJob;
use App\Jobs\ProcessCampaignJob;
use App\Models\Campaign;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignService implements CampaignServiceInterface
{
    /**
     * Create a new campaign
     *
     * @param  array<string, mixed>  $data
     */
    public function createCampaign(Tenant $tenant, array $data): Campaign
    {
        // Initialize tenant context
        tenancy()->initialize($tenant);

        return DB::transaction(function () use ($tenant, $data) {
            $campaign = Campaign::create([
                'tenant_id' => $tenant->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'message_template_id' => $data['message_template_id'],
                'subscriber_list_id' => $data['subscriber_list_id'],
                'status' => 'draft',
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'created_by' => auth()->id() ?? $data['created_by'] ?? null,
            ]);

            Log::info("Campaign {$campaign->id} created by user {$campaign->created_by}");

            return $campaign;
        });
    }

    /**
     * Schedule a campaign for sending
     */
    public function scheduleCampaign(Campaign $campaign, ?\DateTimeInterface $scheduledAt = null): Campaign
    {
        // Initialize tenant context
        tenancy()->initialize($campaign->tenant);

        // Validate campaign
        $validation = $this->validateCampaign($campaign);
        if (! $validation['valid']) {
            throw new \Exception('Campaign validation failed: '.implode(', ', $validation['errors']));
        }

        $campaign->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt ?? now(),
        ]);

        Log::info("Campaign {$campaign->id} scheduled for ".$campaign->scheduled_at);

        return $campaign->fresh();
    }

    /**
     * Start sending a campaign
     */
    public function sendCampaign(Campaign $campaign): bool
    {
        // Initialize tenant context
        tenancy()->initialize($campaign->tenant);

        // Validate campaign
        $validation = $this->validateCampaign($campaign);
        if (! $validation['valid']) {
            throw new \Exception('Campaign validation failed: '.implode(', ', $validation['errors']));
        }

        // Check if campaign can be sent
        if (! $campaign->canBeSent()) {
            throw new \Exception("Campaign {$campaign->id} cannot be sent. Current status: {$campaign->status}");
        }

        // Dispatch campaign processing job
        ProcessCampaignJob::dispatch($campaign);

        // Start monitoring progress
        MonitorCampaignProgressJob::dispatch($campaign)
            ->delay(now()->addSeconds(30));

        Log::info("Campaign {$campaign->id} send initiated");

        return true;
    }

    /**
     * Pause an active campaign
     */
    public function pauseCampaign(Campaign $campaign): Campaign
    {
        tenancy()->initialize($campaign->tenant);

        if (! $campaign->isSending()) {
            throw new \Exception("Campaign {$campaign->id} is not currently sending");
        }

        $campaign->update(['status' => 'paused']);

        Log::info("Campaign {$campaign->id} paused");

        return $campaign->fresh();
    }

    /**
     * Resume a paused campaign
     */
    public function resumeCampaign(Campaign $campaign): Campaign
    {
        tenancy()->initialize($campaign->tenant);

        if (! $campaign->isPaused()) {
            throw new \Exception("Campaign {$campaign->id} is not paused");
        }

        // Re-dispatch pending messages
        $pendingMessages = $campaign->campaignMessages()->where('status', 'pending')->get();

        foreach ($pendingMessages as $message) {
            \App\Jobs\SendCampaignMessageJob::dispatch($message);
        }

        $campaign->update(['status' => 'sending']);

        // Resume monitoring
        MonitorCampaignProgressJob::dispatch($campaign)
            ->delay(now()->addSeconds(30));

        Log::info("Campaign {$campaign->id} resumed with {$pendingMessages->count()} pending messages");

        return $campaign->fresh();
    }

    /**
     * Cancel a campaign
     */
    public function cancelCampaign(Campaign $campaign): Campaign
    {
        tenancy()->initialize($campaign->tenant);

        if ($campaign->isCompleted()) {
            throw new \Exception("Campaign {$campaign->id} is already completed");
        }

        // Mark all pending messages as failed
        $campaign->campaignMessages()
            ->where('status', 'pending')
            ->update([
                'status' => 'failed',
                'error_message' => 'Campaign cancelled',
            ]);

        $campaign->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);

        Log::info("Campaign {$campaign->id} cancelled");

        return $campaign->fresh();
    }

    /**
     * Get campaign statistics
     *
     * @return array<string, mixed>
     */
    public function getCampaignStats(Campaign $campaign): array
    {
        return [
            'total_recipients' => $campaign->total_recipients,
            'sent_count' => $campaign->sent_count,
            'delivered_count' => $campaign->delivered_count,
            'failed_count' => $campaign->failed_count,
            'opened_count' => $campaign->opened_count,
            'clicked_count' => $campaign->clicked_count,
            'pending_count' => $campaign->campaignMessages()->where('status', 'pending')->count(),
            'success_rate' => $campaign->getSuccessRate(),
            'open_rate' => $campaign->getOpenRate(),
            'click_rate' => $campaign->getClickRate(),
            'status' => $campaign->status,
            'started_at' => $campaign->started_at?->toIso8601String(),
            'completed_at' => $campaign->completed_at?->toIso8601String(),
        ];
    }

    /**
     * Get subscribers for a campaign
     */
    public function getCampaignRecipients(Campaign $campaign): Collection
    {
        /** @var \App\Models\SubscriberList|null $subscriberList */
        $subscriberList = $campaign->subscriberList;
        if (! $subscriberList) {
            return new Collection;
        }

        return $subscriberList->subscribers()
            ->where('status', 'active')
            ->whereNotNull('opt_in_date')
            ->whereNull('opt_out_date')
            ->get();
    }

    /**
     * Validate campaign before sending
     *
     * @return array<string, mixed> ['valid' => bool, 'errors' => array]
     */
    public function validateCampaign(Campaign $campaign): array
    {
        $errors = [];

        // Check if campaign has a name
        if (empty($campaign->name)) {
            $errors[] = 'Campaign must have a name';
        }

        // Check if campaign has a template
        if (empty($campaign->message_template_id)) {
            $errors[] = 'Campaign must have a message template';
        } else {
            $template = $campaign->messageTemplate;

            // Check if template is active
            if (! $template->is_active) {
                $errors[] = 'Message template is not active';
            }

            // Validate template syntax
            $templateRenderer = app(\App\Contracts\Services\TemplateRendererInterface::class);
            $templateValidation = $templateRenderer->validateTemplate($template);

            if (! $templateValidation['valid']) {
                $errors = array_merge($errors, $templateValidation['errors']);
            }
        }

        // Check if campaign has a subscriber list
        if (empty($campaign->subscriber_list_id)) {
            $errors[] = 'Campaign must have a subscriber list';
        } else {
            $recipientCount = $this->getCampaignRecipients($campaign)->count();

            if ($recipientCount === 0) {
                $errors[] = 'Subscriber list has no active subscribers';
            }
        }

        // Check if tenant has sufficient credits
        if ($campaign->messageTemplate) {
            $recipientCount = $this->getCampaignRecipients($campaign)->count();
            $channel = $campaign->messageTemplate->channel;
            $creditManagement = app(\App\Contracts\Services\CreditManagementInterface::class);

            // Estimate cost (1 credit per message for now)
            $estimatedCost = $recipientCount;

            if (! $creditManagement->canUseChannel($campaign->tenant, $channel, $estimatedCost)) {
                $available = $creditManagement->getAvailableCredits($campaign->tenant, $channel);
                $errors[] = "Insufficient {$channel} credits. Required: {$estimatedCost}, Available: {$available}";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
