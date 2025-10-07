<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\WorkflowTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkflowTemplateTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_can_create_workflow_template_with_customization_notes()
    {
        $workflow = WorkflowTemplate::create([
            'uuid' => 'test-uuid',
            'name' => 'Customer debt collection',
            'template_type' => 'industry',
            'template_version' => '1.0',
            'industry_category' => 'financial_services',
            'complexity_level' => 'medium',
            'description' => 'Testing the workflow create',
            'customization_notes' => 'Test customization notes',
            'change_log' => 'Initial version',
            'email_enabled' => true,
            'sms_enabled' => false,
            'whatsapp_enabled' => false,
            'voice_enabled' => false,
            'estimated_duration_days' => 7,
            'access_scope_id' => 1,
            'tags' => '["debtCollection"]',
            'is_published' => false,
            'is_active' => true,
            'published_at' => null,
            'is_public' => false,
            'is_system_template' => false,
            'is_customizable' => true,
            'locked_milestones' => '[]',
            'status_type_id' => 1,
            'user_id' => 1,
            'created_by' => 1,
        ]);
        
        $this->assertDatabaseHas('workflow_templates', [
            'name' => 'Customer debt collection',
            'customization_notes' => 'Test customization notes'
        ]);
    }
}