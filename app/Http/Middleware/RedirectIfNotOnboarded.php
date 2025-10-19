<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotOnboarded
{
    /**
     * Handle an incoming request.
     *
     * Redirect authenticated users to onboarding if they haven't completed it yet
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (auth()->check()) {
            $user = auth()->user();

            // Check if user needs to complete onboarding
            $needsOnboarding = ! $user->tenant_id || ! $user->tenant?->onboarding_completed;

            // If they need onboarding and they're not already on the onboarding route
            if ($needsOnboarding && ! $request->is('onboarding*')) {
                return redirect()->route('onboarding.index');
            }
        }

        return $next($request);
    }
}
