<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class InspectRedis extends Command
{
    protected $signature = 'redis:inspect {pattern?} {--limit=20} {--delete}';
    protected $description = 'Inspect Redis cache keys and values';

    public function handle(): int
    {
        $pattern = $this->argument('pattern') ?? '*';
        $limit = (int) $this->option('limit');
        $delete = $this->option('delete');

        try {
            // Get Redis connection for cache
            $redis = Redis::connection('cache');
            
            // Get keys matching pattern
            $keys = $redis->keys($pattern);
            
            if (empty($keys)) {
                $this->warn("No keys found matching pattern: {$pattern}");
                return 0;
            }

            $this->info("Found " . count($keys) . " keys matching pattern: {$pattern}");
            $this->newLine();

            // Display keys with values
            $displayed = 0;
            foreach ($keys as $key) {
                if ($displayed >= $limit && !$delete) {
                    $this->warn("Showing first {$limit} keys only. Use --limit option to show more.");
                    break;
                }

                $this->line("🔑 <fg=yellow>{$key}</fg=yellow>");
                
                if ($delete) {
                    $redis->del($key);
                    $this->line("   <fg=red>✗ DELETED</fg=red>");
                } else {
                    // Try to get the value using Laravel Cache facade for proper deserialization
                    $cacheKey = str_replace('commatix_database_commatix_', '', $key);
                    $value = Cache::get($cacheKey);
                    $ttl = $redis->ttl($key);
                    
                    if ($value === null) {
                        $this->line("   <fg=gray>Value: (not found or expired)</fg=gray>");
                    } else {
                        if (is_object($value) || is_array($value)) {
                            $this->line("   <fg=green>Type: " . gettype($value) . "</fg=green>");
                            if (is_countable($value)) {
                                $this->line("   <fg=green>Count: " . count($value) . "</fg=green>");
                            }
                            $preview = json_encode($value, JSON_PRETTY_PRINT);
                            $this->line("   <fg=cyan>Preview: " . substr($preview, 0, 300) . (strlen($preview) > 300 ? '...' : '') . "</fg=cyan>");
                        } else {
                            $this->line("   <fg=green>Value: " . substr((string)$value, 0, 200) . "</fg=green>");
                        }
                    }
                    
                    if ($ttl > 0) {
                        $hours = floor($ttl / 3600);
                        $minutes = floor(($ttl % 3600) / 60);
                        $seconds = $ttl % 60;
                        $timeFormat = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                        $this->line("   <fg=blue>TTL: {$ttl} seconds ({$timeFormat})</fg=blue>");
                    } elseif ($ttl === -1) {
                        $this->line("   <fg=blue>TTL: Never expires</fg=blue>");
                    } else {
                        $this->line("   <fg=red>TTL: Expired or not set</fg=red>");
                    }
                }
                
                $this->newLine();
                $displayed++;
            }

            if ($delete) {
                $this->info("Deleted {$displayed} keys matching pattern: {$pattern}");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Error inspecting Redis: " . $e->getMessage());
            return 1;
        }
    }
}