<?php

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OnboardingControllerModern;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/**
 * Public Routes
 */
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Quick test route for modern onboarding (remove after testing)
Route::get('/test-onboarding', function () {
    return view('onboarding.step1-modern', [
        'currentStep' => 1,
        'totalSteps' => 6,
        'progressPercentage' => 17,
        'stepData' => [],
        'provinces' => [
            'GP' => 'Gauteng',
            'WC' => 'Western Cape',
            'KZN' => 'KwaZulu-Natal',
        ],
        'industries' => collect([
            (object)['id' => 1, 'name' => 'Technology'],
            (object)['id' => 2, 'name' => 'Healthcare'],
            (object)['id' => 3, 'name' => 'Finance'],
        ]),
    ]);
});

/**
 * Onboarding Routes (auth required)
 */
Route::middleware('auth')->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('index');
    Route::get('/step/{step}', [OnboardingController::class, 'showStep'])->name('step');
    Route::post('/step/{step}', [OnboardingController::class, 'processStep'])->name('process');
});

/**
 * Modern Onboarding Routes (Monday.com style - auth required)
 */
Route::middleware('auth')->prefix('onboarding-modern')->name('onboarding.modern.')->group(function () {
    Route::get('/', [OnboardingControllerModern::class, 'index'])->name('index');
    Route::get('/step/{step}', [OnboardingControllerModern::class, 'showStep'])->name('step');
    Route::post('/step/{step}', [OnboardingControllerModern::class, 'processStep'])->name('process');
    Route::get('/complete', [OnboardingControllerModern::class, 'complete'])->name('complete');
});

/**
 * Filament Panel Routes
 *
 * Filament handles:
 * - /dashboard (main panel - requires auth)
 * - /dashboard/login (login page)
 * - /dashboard/register (registration page)
 */

// Named login route redirect for Laravel's auth middleware
Route::get('/login', function () {
    return redirect('/dashboard/login');
})->name('login');
