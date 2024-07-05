<?php

namespace App\Providers;

use App\Models\MailConfig;
use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $mailConfig = MailConfig::first();
        if($mailConfig){

            config(['mail.driver' => $mailConfig->driver]);
            config(['mail.host' => $mailConfig->host]);
            config(['mail.port' => $mailConfig->port]);
            config(['mail.encryption' => $mailConfig->encryption]);
            config(['mail.username' => $mailConfig->username]);
            config(['mail.password' => $mailConfig->password]);
            config(['mail.from.address' => $mailConfig->from_address]);
            config(['mail.from.name' => $mailConfig->from_name]);
        }
    }
}
