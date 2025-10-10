<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        // Register Blade components
        Blade::component('feature-status-badge', \App\Filament\Components\FeatureStatusBadge::class);

        // Pulse authorization
        Gate::define('viewPulse', function ($user) {
            // Allow in local environment
            if (app()->environment('local')) {
                return true;
            }

            // Allow Super Admins
            return $user->hasRole('super_admin');
        });
    }
}
