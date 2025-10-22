<?php

namespace App\Jobs;

use App\Contracts\Services\CreditManagementInterface;
use App\Contracts\Services\MessageSenderInterface;
use App\Contracts\Services\TemplateRendererInterface;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCampaignMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public CampaignMessage $campaignMessage
    ) {
        // Set queue based on priority
        $this->onQueue('campaigns');
    }

    /**
     * Execute the job.
     */
    public function handle(
        TemplateRendererInterface $templateRenderer,
        CreditManagementInterface $creditManagement
    ): void {
        $message = $this->campaignMessage;
        $campaign = $message->campaign;
        $subscriber = $message->subscriber;
        $template = $campaign->messageTemplate;
        $tenant = $campaign->tenant;

        // Initialize tenant context
        tenancy()->initialize($tenant);

        try {
            // Check if campaign is still active
            /** @var \App\Models\Campaign $campaign */
            if (! $campaign->isSending()) {
                Log::info("Campaign {$campaign->id} is no longer sending. Skipping message {$message->id}");

                return;
            }

            // Check if subscriber is still active
            if ($subscriber->status !== 'active') {
                $message->markAsFailed('Subscriber is not active');

                return;
            }

            // Get the appropriate sender for this channel
            $sender = app()->make('message_sender.'.$template->channel);

            if (! $sender instanceof MessageSenderInterface) {
                throw new \Exception("Invalid message sender for channel: {$template->channel}");
            }

            // Validate before sending
            $validation = $sender->validate($subscriber, $template);
            if (! $validation['valid']) {
                $message->markAsFailed($validation['error'] ?? 'Validation failed');

                return;
            }

            // Check credits
            $cost = $sender->getCostPerMessage();
            if (! $creditManagement->canUseChannel($tenant, $template->channel, $cost)) {
                $message->markAsFailed('Insufficient credits');
                Log::warning("Insufficient {$template->channel} credits for tenant {$tenant->id}");

                return;
            }

            // Render template
            $rendered = $templateRenderer->render($template, $subscriber, [
                'campaign_name' => $campaign->name,
            ]);

            // Send message
            $result = $sender->send($subscriber, $template, [
                'subject' => $rendered['subject'],
                'content' => $rendered['content'],
            ]);

            if ($result['success']) {
                // Mark as sent
                $message->markAsSent(
                    $result['message_id'] ?? 'unknown',
                    $result
                );

                // Deduct credits
                $creditManagement->deductCredits($tenant, $template->channel, $cost);

                Log::info("Sent message {$message->id} for campaign {$campaign->id} to subscriber {$subscriber->id}");
            } else {
                // Mark as failed
                $message->markAsFailed($result['error'] ?? 'Unknown error', $result);
                Log::error("Failed to send message {$message->id}: ".$result['error']);
            }
        } catch (\Exception $e) {
            Log::error("Exception sending message {$message->id}: ".$e->getMessage());
            $message->markAsFailed($e->getMessage());

            // Re-throw to trigger retry logic
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed after all retries for message {$this->campaignMessage->id}: ".$exception->getMessage());

        $this->campaignMessage->markAsFailed(
            'Failed after '.($this->tries).' attempts: '.$exception->getMessage()
        );
    }
}
