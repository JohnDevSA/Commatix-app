<?php

namespace App\Services;

use App\Models\Milestone;
use App\Models\MilestoneDocumentAttachment;
use App\Models\WorkflowTemplate;

class WorkflowExportService
{
    /**
     * Export a workflow to the format matching the provided structure
     */
    public function exportWorkflow(WorkflowTemplate $workflow)
    {
        $exportData = [];

        foreach ($workflow->milestones()->orderBy('sequence_order')->get() as $milestone) {
            // Get document attachments for this milestone
            $attachments = $milestone->documentAttachments;

            if ($attachments->count() > 0) {
                foreach ($attachments as $attachment) {
                    $exportData[] = $this->formatMilestoneData($workflow, $milestone, $attachment);
                }
            } else {
                // Add milestone entry even without attachments
                $exportData[] = $this->formatMilestoneData($workflow, $milestone);
            }

            // Add milestone results if any
            $results = $milestone->milestoneResults;
            if ($results->count() > 0) {
                // Results would be handled in a similar way if needed
            }
        }

        return $exportData;
    }

    /**
     * Format milestone data to match the export structure
     */
    private function formatMilestoneData(WorkflowTemplate $workflow, Milestone $milestone, ?MilestoneDocumentAttachment $attachment = null)
    {
        return [
            'WORKFLOW_ID' => $workflow->id,
            'WORKFLOW_NAME' => $workflow->name,
            'MILESTONE_ID' => $milestone->id,
            'MILESTONE' => $milestone->name,
            'HINT' => $milestone->hint,
            'STEP_NUMBER' => $milestone->sequence_order,
            'SLA_DAYS' => $milestone->sla_days,
            'SLA_HOURS' => $milestone->sla_hours,
            'SLA_MINUTES' => $milestone->sla_minutes,
            'APPROVAL_GROUP_ID' => $milestone->approval_group_id,
            'APPROVAL_GROUP' => $milestone->approval_group_name,
            'APPROVAL_LEVEL_REQUIRED' => $milestone->approval_group_id, // Simplified - would need actual approvers logic
            'APPROVERS' => '', // Would need to fetch actual approvers
            'ATT_ID' => $attachment ? $attachment->document_type_id : '',
            'ATTATCHMENT_NAME' => $attachment ? $attachment->attachment_name : '',
            'ATTATCHMENT_REQUIRED' => $attachment ? ($attachment->is_required ? 'YES' : 'NO') : '',
            'TEMPLATE_ID' => $attachment ? $attachment->template_id : '',
            'MILESTONE_RESULT' => '', // Would be populated with milestone results
            'IS_DEFAULT' => '', // Would indicate if this is a default result
        ];
    }

    /**
     * Import workflow data from the provided structure
     */
    public function importWorkflow(array $importData)
    {
        // Group by workflow ID to handle multiple workflows
        $workflows = collect($importData)->groupBy('WORKFLOW_ID');

        foreach ($workflows as $workflowId => $milestoneData) {
            // Find or create workflow
            $workflow = WorkflowTemplate::find($workflowId);

            if (! $workflow) {
                // Create new workflow if it doesn't exist
                $workflow = WorkflowTemplate::create([
                    'name' => $milestoneData->first()['WORKFLOW_NAME'],
                    // Add other workflow fields as needed
                ]);
            }

            // Group milestone data by milestone ID
            $milestones = $milestoneData->groupBy('MILESTONE_ID');

            foreach ($milestones as $milestoneId => $data) {
                $milestoneInfo = $data->first();

                // Find or create milestone
                $milestone = Milestone::find($milestoneId);

                if (! $milestone) {
                    $milestone = Milestone::create([
                        'workflow_template_id' => $workflow->id,
                        'name' => $milestoneInfo['MILESTONE'],
                        'hint' => $milestoneInfo['HINT'],
                        'sequence_order' => $milestoneInfo['STEP_NUMBER'],
                        'sla_days' => $milestoneInfo['SLA_DAYS'],
                        'sla_hours' => $milestoneInfo['SLA_HOURS'],
                        'sla_minutes' => $milestoneInfo['SLA_MINUTES'],
                        'approval_group_id' => $milestoneInfo['APPROVAL_GROUP_ID'],
                        'approval_group_name' => $milestoneInfo['APPROVAL_GROUP'],
                    ]);
                }

                // Process attachments for this milestone
                foreach ($data as $attachmentData) {
                    if (! empty($attachmentData['ATT_ID'])) {
                        MilestoneDocumentAttachment::updateOrCreate(
                            [
                                'milestone_id' => $milestone->id,
                                'document_type_id' => $attachmentData['ATT_ID'],
                            ],
                            [
                                'attachment_name' => $attachmentData['ATTATCHMENT_NAME'],
                                'is_required' => $attachmentData['ATTATCHMENT_REQUIRED'] === 'YES',
                                'template_id' => $attachmentData['TEMPLATE_ID'] ?? null,
                            ]
                        );
                    }
                }
            }
        }

        return true;
    }
}
