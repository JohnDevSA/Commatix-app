<?php

namespace Tests\Unit;

use App\Models\Milestone;
use App\Models\WorkflowTemplate;
use App\Services\WorkflowExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MilestoneExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_export_milestone_with_sla_time_fields()
    {
        // Create a workflow template
        $workflow = WorkflowTemplate::create([
            'uuid' => 'test-uuid',
            'name' => 'Test Workflow',
            'description' => 'Test Description',
            'access_scope_id' => 1,
            'template_type' => 'custom',
            'created_by' => 1,
            'status_type_id' => 1,
            'user_id' => 1,
        ]);

        // Create a milestone with SLA time fields
        $milestone = Milestone::create([
            'workflow_template_id' => $workflow->id,
            'name' => 'Test Milestone',
            'sla_days' => 1,
            'sla_hours' => 2,
            'sla_minutes' => 30,
            'approval_group_id' => 123,
            'approval_group_name' => 'Test Approval Group',
            'status_id' => 1,
            'status_type_id' => 1,
            'requires_docs' => false,
            'actions' => '{}',
        ]);

        // Create export service
        $exportService = new WorkflowExportService;

        // Export the workflow
        $exportData = $exportService->exportWorkflow($workflow);

        // Verify the export contains the expected data
        $this->assertNotEmpty($exportData);
        $this->assertEquals($workflow->id, $exportData[0]['WORKFLOW_ID']);
        $this->assertEquals('Test Workflow', $exportData[0]['WORKFLOW_NAME']);
        $this->assertEquals($milestone->id, $exportData[0]['MILESTONE_ID']);
        $this->assertEquals('Test Milestone', $exportData[0]['MILESTONE']);
        $this->assertEquals(1, $exportData[0]['SLA_DAYS']);
        $this->assertEquals(2, $exportData[0]['SLA_HOURS']);
        $this->assertEquals(30, $exportData[0]['SLA_MINUTES']);
        $this->assertEquals(123, $exportData[0]['APPROVAL_GROUP_ID']);
        $this->assertEquals('Test Approval Group', $exportData[0]['APPROVAL_GROUP']);
    }
}
