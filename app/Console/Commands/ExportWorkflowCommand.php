<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkflowTemplate;
use App\Services\WorkflowExportService;
use Illuminate\Support\Facades\Storage;

class ExportWorkflowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:export {workflowId} {--format=csv : Export format (csv, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export a workflow to match the provided structure';

    /**
     * Execute the console command.
     */
    public function handle(WorkflowExportService $exportService)
    {
        $workflowId = $this->argument('workflowId');
        $format = $this->option('format');
        
        $workflow = WorkflowTemplate::find($workflowId);
        
        if (!$workflow) {
            $this->error("Workflow with ID {$workflowId} not found.");
            return 1;
        }
        
        $exportData = $exportService->exportWorkflow($workflow);
        
        if ($format === 'json') {
            $this->exportAsJson($exportData, $workflowId);
        } else {
            $this->exportAsCsv($exportData, $workflowId);
        }
        
        $this->info("Workflow {$workflowId} exported successfully.");
        return 0;
    }
    
    private function exportAsCsv(array $data, int $workflowId)
    {
        $filename = "workflow_{$workflowId}_export.csv";
        $handle = fopen("php://temp", "w");
        
        // Write header
        if (!empty($data)) {
            fputcsv($handle, array_keys($data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        Storage::disk('local')->put($filename, $csvContent);
        $this->info("Export saved to: " . Storage::disk('local')->path($filename));
    }
    
    private function exportAsJson(array $data, int $workflowId)
    {
        $filename = "workflow_{$workflowId}_export.json";
        $jsonContent = json_encode($data, JSON_PRETTY_PRINT);
        
        Storage::disk('local')->put($filename, $jsonContent);
        $this->info("Export saved to: " . Storage::disk('local')->path($filename));
    }
}