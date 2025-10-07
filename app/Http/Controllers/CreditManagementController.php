<?php

namespace App\Http\Controllers;

use App\Interfaces\CreditManagementInterface;
use App\Models\Tenant;
use Illuminate\Http\Request;

class CreditManagementController extends Controller
{
    private CreditManagementInterface $creditManagementService;

    public function __construct(CreditManagementInterface $creditManagementService)
    {
        $this->creditManagementService = $creditManagementService;
    }

    /**
     * Get tenant credits for a specific channel
     */
    public function getTenantCredits(Request $request, string $tenantId, string $channel)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $credits = $this->creditManagementService->getTenantCredits($tenant, $channel);

        return response()->json([
            'tenant_id' => $tenantId,
            'channel' => $channel,
            'credits' => $credits
        ]);
    }

    /**
     * Add credits to tenant account (top-up)
     */
    public function topUpCredits(Request $request, string $tenantId)
    {
        $request->validate([
            'channel' => 'required|in:sms,email,whatsapp,voice',
            'credits' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $tenant = Tenant::findOrFail($tenantId);
        
        $success = $this->creditManagementService->addCredits(
            $tenant, 
            $request->channel, 
            $request->credits
        );

        if ($success) {
            return response()->json([
                'message' => 'Credits added successfully',
                'tenant_id' => $tenantId,
                'channel' => $request->channel,
                'credits_added' => $request->credits
            ]);
        }

        return response()->json([
            'error' => 'Failed to add credits'
        ], 400);
    }

    /**
     * Check if tenant can use a channel
     */
    public function canUseChannel(Request $request, string $tenantId, string $channel)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $canUse = $this->creditManagementService->canUseChannel($tenant, $channel);

        return response()->json([
            'tenant_id' => $tenantId,
            'channel' => $channel,
            'can_use' => $canUse,
            'credits' => $this->creditManagementService->getTenantCredits($tenant, $channel)
        ]);
    }
}