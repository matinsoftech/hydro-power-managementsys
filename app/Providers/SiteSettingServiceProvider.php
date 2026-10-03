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
        try {
            $siteSetting = SiteSetting::first();
        } catch (\Throwable $e) {
            $siteSetting = null;
        }

        App::singleton('siteSetting', function () use ($siteSetting) {
            return $siteSetting;
        });
    }
}
