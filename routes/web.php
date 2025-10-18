<?php

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/**
 * Public Routes
 */
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

/**
 * Onboarding Routes (auth required)
 */
Route::middleware('auth')->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('index');
    Route::get('/step/{step}', [OnboardingController::class, 'showStep'])->name('step');
    Route::post('/step/{step}', [OnboardingController::class, 'processStep'])->name('process');
});

/**
 * Filament Panel Routes
 *
 * Filament handles:
 * - /dashboard (main panel - requires auth)
 * - /dashboard/login (login page)
 * - /dashboard/register (registration page)
 */
