<?php

namespace App\Contracts\Services;

use App\Models\Campaign;
use App\Models\Tenant;
use Illuminate\Support\Collection;

interface CampaignServiceInterface
{
    /**
     * Create a new campaign
     *
     * @param  array<string, mixed>  $data
     */
    public function createCampaign(Tenant $tenant, array $data): Campaign;

    /**
     * Schedule a campaign for sending
     */
    public function scheduleCampaign(Campaign $campaign, ?\DateTimeInterface $scheduledAt = null): Campaign;

    /**
     * Start sending a campaign
     */
    public function sendCampaign(Campaign $campaign): bool;

    /**
     * Pause an active campaign
     */
    public function pauseCampaign(Campaign $campaign): Campaign;

    /**
     * Resume a paused campaign
     */
    public function resumeCampaign(Campaign $campaign): Campaign;

    /**
     * Cancel a campaign
     */
    public function cancelCampaign(Campaign $campaign): Campaign;

    /**
     * Get campaign statistics
     *
     * @return array<string, mixed>
     */
    public function getCampaignStats(Campaign $campaign): array;

    /**
     * Get subscribers for a campaign
     */
    public function getCampaignRecipients(Campaign $campaign): Collection;

    /**
     * Validate campaign before sending
     *
     * @return array<string, mixed> ['valid' => bool, 'errors' => array]
     */
    public function validateCampaign(Campaign $campaign): array;
}
