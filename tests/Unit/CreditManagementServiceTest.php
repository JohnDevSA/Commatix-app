<?php

namespace Tests\Unit;

use App\Contracts\Services\CreditManagementInterface;
use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\TenantCommunicationUsage;
use App\Models\TenantTopUp;
use App\Services\Billing\CreditManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CreditManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    private CreditManagementService $service;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CreditManagementService();
        $this->tenant = Tenant::factory()->create();
    }

    public function test_implements_interface()
    {
        $this->assertInstanceOf(CreditManagementInterface::class, $this->service);
    }

    public function test_can_use_channel_returns_true_when_credits_available()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'tenant_id' => $this->tenant->id,
            'sms_limit' => 100,
            'status' => 'active',
        ]);

        TenantCommunicationUsage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'sms_sent' => 20,
        ]);

        // Act
        $canUse = $this->service->canUseChannel($this->tenant, 'sms', 10);

        // Assert
        $this->assertTrue($canUse);
    }

    public function test_can_use_channel_returns_false_when_insufficient_credits()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'tenant_id' => $this->tenant->id,
            'sms_limit' => 100,
            'status' => 'active',
        ]);

        TenantCommunicationUsage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'sms_sent' => 95,
        ]);

        // Act
        $canUse = $this->service->canUseChannel($this->tenant, 'sms', 10);

        // Assert
        $this->assertFalse($canUse);
    }

    public function test_deduct_credits_successfully()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'tenant_id' => $this->tenant->id,
            'email_limit' => 500,
            'status' => 'active',
        ]);

        $usage = TenantCommunicationUsage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'email_sent' => 100,
        ]);

        // Act
        $result = $this->service->deductCredits($this->tenant, 'email', 50);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(150, $usage->fresh()->email_sent);
    }

    public function test_deduct_credits_throws_exception_when_insufficient()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'tenant_id' => $this->tenant->id,
            'whatsapp_limit' => 50,
            'status' => 'active',
        ]);

        TenantCommunicationUsage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'whatsapp_sent' => 45,
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient whatsapp credits');

        $this->service->deductCredits($this->tenant, 'whatsapp', 10);
    }

    public function test_add_credits_creates_top_up_record()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'tenant_id' => $this->tenant->id,
            'sms_limit' => 100,
            'status' => 'active',
        ]);

        // Act
        $result = $this->service->addCredits($this->tenant, 'sms', 500, 'Monthly top-up');

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(1, TenantTopUp::count());

        $topUp = TenantTopUp::first();
        $this->assertEquals($this->tenant->id, $topUp->tenant_id);
        $this->assertEquals('sms', $topUp->channel);
        $this->assertEquals(500, $topUp->amount);
        $this->assertEquals('Monthly top-up', $topUp->reason);
    }

    public function test_get_available_credits_includes_top_ups()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'tenant_id' => $this->tenant->id,
            'sms_limit' => 100,
            'status' => 'active',
        ]);

        TenantCommunicationUsage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'sms_sent' => 30,
        ]);

        TenantTopUp::factory()->create([
            'tenant_id' => $this->tenant->id,
            'channel' => 'sms',
            'amount' => 200,
            'created_at' => now()->startOfMonth()->addDay(),
        ]);

        // Act
        $available = $this->service->getAvailableCredits($this->tenant, 'sms');

        // Assert - (100 base + 200 topup) - 30 used = 270
        $this->assertEquals(270, $available);
    }

    public function test_get_current_usage_returns_correct_amount()
    {
        // Arrange
        TenantCommunicationUsage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'voice_sent' => 75,
        ]);

        // Act
        $usage = $this->service->getCurrentUsage($this->tenant, 'voice');

        // Assert
        $this->assertEquals(75, $usage);
    }

    public function test_throws_exception_for_invalid_channel()
    {
        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid channel');

        $this->service->canUseChannel($this->tenant, 'invalid_channel', 10);
    }

    public function test_cache_is_cleared_after_deducting_credits()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'tenant_id' => $this->tenant->id,
            'sms_limit' => 100,
            'status' => 'active',
        ]);

        TenantCommunicationUsage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'sms_sent' => 10,
        ]);

        // Prime the cache
        $this->service->getAvailableCredits($this->tenant, 'sms');

        // Act
        $this->service->deductCredits($this->tenant, 'sms', 5);

        // Assert - Get fresh data (cache should be cleared)
        $available = $this->service->getAvailableCredits($this->tenant, 'sms');
        $this->assertEquals(85, $available); // 100 - 15 (10 initial + 5 deducted)
    }

    public function test_handles_tenant_without_active_subscription()
    {
        // No subscription created

        // Act
        $available = $this->service->getAvailableCredits($this->tenant, 'sms');

        // Assert
        $this->assertEquals(0, $available);
    }

    public function test_supports_all_communication_channels()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'tenant_id' => $this->tenant->id,
            'sms_limit' => 100,
            'email_limit' => 200,
            'whatsapp_limit' => 300,
            'voice_limit' => 400,
            'status' => 'active',
        ]);

        TenantCommunicationUsage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        // Act & Assert
        $this->assertEquals(100, $this->service->getAvailableCredits($this->tenant, 'sms'));
        $this->assertEquals(200, $this->service->getAvailableCredits($this->tenant, 'email'));
        $this->assertEquals(300, $this->service->getAvailableCredits($this->tenant, 'whatsapp'));
        $this->assertEquals(400, $this->service->getAvailableCredits($this->tenant, 'voice'));
    }
}