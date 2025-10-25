<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TestKubernetesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $worker = gethostname();
        $timestamp = now()->toDateTimeString();

        \Log::info('🚀 Test job processed by Kubernetes worker!', [
            'timestamp' => $timestamp,
            'worker' => $worker,
        ]);

        // Also write to a file for easy verification
        file_put_contents(
            storage_path('logs/k8s-test.log'),
            "[$timestamp] Job processed by K8s worker: $worker\n",
            FILE_APPEND
        );

        echo "✅ Job processed successfully by $worker at $timestamp\n";
    }
}
