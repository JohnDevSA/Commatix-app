<?php

use App\Http\Controllers\Api\TaskSchedulingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Task Scheduling API Routes
Route::middleware(['auth:sanctum'])->prefix('tasks')->group(function () {
    Route::post('/schedule', [TaskSchedulingController::class, 'scheduleTasksForSubscribers'])
        ->name('api.tasks.schedule');

    Route::get('/scheduled/{subscriberListId}', [TaskSchedulingController::class, 'getScheduledTasks'])
        ->name('api.tasks.scheduled');

    Route::get('/statistics', [TaskSchedulingController::class, 'getStatistics'])
        ->name('api.tasks.statistics');
});
