<?php

namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonitorCampaignProgressJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Campaign $campaign
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $campaign = $this->campaign;

        // Initialize tenant context
        tenancy()->initialize($campaign->tenant);

        // Check if campaign is still sending
        if (! $campaign->isSending()) {
            return;
        }

        // Count pending messages
        $pendingCount = $campaign->campaignMessages()
            ->where('status', 'pending')
            ->count();

        // If no pending messages, mark campaign as completed
        if ($pendingCount === 0) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            Log::info("Campaign {$campaign->id} completed. Sent: {$campaign->sent_count}, Delivered: {$campaign->delivered_count}, Failed: {$campaign->failed_count}");
        } else {
            // Re-check in 30 seconds
            MonitorCampaignProgressJob::dispatch($campaign)
                ->delay(now()->addSeconds(30));
        }
    }
}
