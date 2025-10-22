<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessCampaignJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // Don't retry campaign processing

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Campaign $campaign
    ) {
        $this->onQueue('campaigns');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $campaign = $this->campaign;

        // Initialize tenant context
        tenancy()->initialize($campaign->tenant);

        try {
            // Check if campaign can be sent
            if (! $campaign->canBeSent()) {
                Log::warning("Campaign {$campaign->id} cannot be sent. Status: {$campaign->status}");

                return;
            }

            // Update status to sending
            $campaign->update([
                'status' => 'sending',
                'started_at' => now(),
            ]);

            // Get all active subscribers from the list
            /** @var \App\Models\SubscriberList $subscriberList */
            $subscriberList = $campaign->subscriberList;
            $subscribers = $subscriberList->subscribers()
                ->where('status', 'active')
                ->whereNotNull('opt_in_date')
                ->whereNull('opt_out_date')
                ->get();

            if ($subscribers->isEmpty()) {
                $campaign->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                ]);
                Log::warning("Campaign {$campaign->id} has no active subscribers");

                return;
            }

            // Update total recipients
            $campaign->update([
                'total_recipients' => $subscribers->count(),
            ]);

            // Create campaign messages for each subscriber
            $messages = [];
            foreach ($subscribers as $subscriber) {
                $messages[] = [
                    'campaign_id' => $campaign->id,
                    'subscriber_id' => $subscriber->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert campaign messages
            DB::table('campaign_messages')->insert($messages);

            // Dispatch individual send jobs
            $campaignMessages = CampaignMessage::where('campaign_id', $campaign->id)
                ->where('status', 'pending')
                ->get();

            Log::info("Dispatching {$campaignMessages->count()} messages for campaign {$campaign->id}");

            foreach ($campaignMessages as $message) {
                SendCampaignMessageJob::dispatch($message)
                    ->onQueue('campaigns')
                    ->delay(now()->addSeconds($this->calculateDelay($campaign, $message)));
            }

            Log::info("Campaign {$campaign->id} processing started with {$campaignMessages->count()} messages");
        } catch (\Exception $e) {
            Log::error("Error processing campaign {$campaign->id}: ".$e->getMessage());

            $campaign->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Calculate delay for rate limiting
     * Spread messages over time to avoid overwhelming providers
     */
    private function calculateDelay(Campaign $campaign, CampaignMessage $message): int
    {
        // Get message position
        $position = CampaignMessage::where('campaign_id', $campaign->id)
            ->where('id', '<=', $message->id)
            ->count();

        // Rate limit: 10 messages per second = 100ms between messages
        return (int) ceil($position * 0.1);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Campaign processing job failed for campaign {$this->campaign->id}: ".$exception->getMessage());

        $this->campaign->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);
    }
}
