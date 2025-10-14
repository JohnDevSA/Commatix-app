<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class FixTaskMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:fix-metadata {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing tenant_id, division_id, and created_by values on existing tasks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Running in DRY-RUN mode. No changes will be made.');
        }

        // Find tasks with missing metadata
        $tasksWithMissingData = Task::where(function ($query) {
            $query->whereNull('tenant_id')
                  ->orWhereNull('division_id')
                  ->orWhereNull('created_by');
        })->get();

        if ($tasksWithMissingData->isEmpty()) {
            $this->info('No tasks found with missing metadata. All good!');

            return self::SUCCESS;
        }

        $this->info("Found {$tasksWithMissingData->count()} tasks with missing metadata.");

        $bar = $this->output->createProgressBar($tasksWithMissingData->count());
        $bar->start();

        $fixed = 0;
        $skipped = 0;

        foreach ($tasksWithMissingData as $task) {
            $changes = [];

            // Fix tenant_id from assigned user
            if (! $task->tenant_id && $task->assigned_to) {
                $assignedUser = User::find($task->assigned_to);
                if ($assignedUser && $assignedUser->tenant_id) {
                    $changes['tenant_id'] = $assignedUser->tenant_id;
                }
            }

            // Fix division_id from assigned user
            if (! $task->division_id && $task->assigned_to) {
                $assignedUser = $assignedUser ?? User::find($task->assigned_to);
                if ($assignedUser && $assignedUser->division_id) {
                    $changes['division_id'] = $assignedUser->division_id;
                }
            }

            // Fix created_by - use assigned_to as fallback
            if (! $task->created_by && $task->assigned_to) {
                $changes['created_by'] = $task->assigned_to;
            }

            // Apply changes
            if (! empty($changes)) {
                if (! $dryRun) {
                    $task->update($changes);
                }
                $fixed++;

                if ($this->output->isVerbose()) {
                    $this->newLine();
                    $this->line("Task ID {$task->id}: ".json_encode($changes));
                }
            } else {
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Fixed: {$fixed}");
        $this->info("Skipped (no changes needed): {$skipped}");

        if ($dryRun) {
            $this->warn('DRY-RUN mode was active. Run without --dry-run to apply changes.');
        } else {
            $this->success('Task metadata has been fixed successfully!');
        }

        return self::SUCCESS;
    }
}
