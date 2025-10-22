<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Show the welcome/homepage.
     */
    public function index(): View|RedirectResponse
    {
        // If user is already authenticated
        if (auth()->check()) {
            // Check if they have completed onboarding
            if (! auth()->user()->tenant_id || ! auth()->user()->tenant?->onboarding_completed) {
                return redirect()->route('onboarding.index');
            }

            return redirect()->to('/dashboard');
        }

        return view('welcome');
    }
}
