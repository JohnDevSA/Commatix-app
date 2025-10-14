<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "\n🔍 PERFORMANCE TEST\n";
echo "==================\n\n";

// Get current user for visibility scope
$user = User::first();
tenancy()->initialize(\App\Models\Tenant::first());

echo "📊 Current Data:\n";
echo "  - Tasks: " . Task::count() . "\n";
echo "  - Users: " . User::count() . "\n\n";

// Test WITHOUT eager loading
echo "❌ WITHOUT Eager Loading:\n";
DB::connection()->enableQueryLog();
$start = microtime(true);

$tasks = Task::query()
    ->visibleTo($user)
    ->limit(100)
    ->get();

// Access relationships (triggers N+1)
foreach ($tasks as $task) {
    $_ = $task->subscriber->email ?? null;
    $_ = $task->workflowTemplate->name ?? null;
    $_ = $task->division->name ?? null;
    $_ = $task->assignedTo->name ?? null;
}

$queriesWithout = count(DB::getQueryLog());
$timeWithout = (microtime(true) - $start) * 1000;
DB::connection()->disableQueryLog();

echo "  Queries: {$queriesWithout}\n";
echo "  Time: " . number_format($timeWithout, 2) . "ms\n\n";

// Test WITH eager loading
echo "✅ WITH Eager Loading:\n";
DB::connection()->enableQueryLog();
$start = microtime(true);

$tasks = Task::query()
    ->visibleTo($user)
    ->with(['subscriber', 'workflowTemplate', 'division', 'assignedTo'])
    ->limit(100)
    ->get();

// Access relationships (already loaded)
foreach ($tasks as $task) {
    $_ = $task->subscriber->email ?? null;
    $_ = $task->workflowTemplate->name ?? null;
    $_ = $task->division->name ?? null;
    $_ = $task->assignedTo->name ?? null;
}

$queriesWith = count(DB::getQueryLog());
$timeWith = (microtime(true) - $start) * 1000;
DB::connection()->disableQueryLog();

echo "  Queries: {$queriesWith}\n";
echo "  Time: " . number_format($timeWith, 2) . "ms\n\n";

// Results
echo "📈 IMPROVEMENT:\n";
$queryReduction = (($queriesWithout - $queriesWith) / $queriesWithout) * 100;
$timeReduction = (($timeWithout - $timeWith) / $timeWithout) * 100;

echo "  Query reduction: " . number_format($queryReduction, 1) . "%\n";
echo "  Time reduction: " . number_format($timeReduction, 1) . "%\n\n";

// Cache test
echo "💾 CACHE TEST:\n";
$start = microtime(true);
cache()->put('test', 'value', 60);
$value = cache()->get('test');
$cacheTime = (microtime(true) - $start) * 1000;
echo "  Driver: " . config('cache.default') . "\n";
echo "  Operation time: " . number_format($cacheTime, 3) . "ms\n";
echo "  Status: " . ($value === 'value' ? '✅ Working' : '❌ Failed') . "\n\n";

echo "🎯 VERDICT:\n";
if ($queryReduction > 50) {
    echo "  ✅ Eager loading is working GREAT!\n";
} elseif ($queryReduction > 20) {
    echo "  ⚠️  Eager loading is working but limited by dataset size\n";
} else {
    echo "  ❌ Eager loading might have issues\n";
}

echo "\n💡 NOTE: With only " . Task::count() . " tasks, improvements are minimal.\n";
echo "   Real gains appear with 100+ records!\n\n";
