<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
        // Remember Me cookie: max 1 year (minutes).
        Auth::guard('web')->setRememberDuration(60 * 24 * 365);

        // Schema::defaultStringLength(191);
    }
}
