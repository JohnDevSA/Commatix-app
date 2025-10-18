<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WelcomeController extends Controller
{
    /**
     * Show the welcome/homepage.
     */
    public function index(): View | RedirectResponse
    {
        // If user is already authenticated, redirect to dashboard
        if (auth()->check()) {
            return redirect()->to('/dashboard');
        }

        return view('welcome');
    }
}
