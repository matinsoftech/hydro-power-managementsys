<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class SiteSettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $siteSetting = SiteSetting::first();
        App::singleton('siteSetting', function () use ($siteSetting) {
            return $siteSetting;
        });
    }
}
