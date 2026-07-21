<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Behind proxies that don't forward X-Forwarded-Proto (e.g. the
        // school's shared reverse proxy in front of the VPS), request
        // scheme detection can't be trusted — force https for every
        // generated URL (assets, routes...) whenever the app is actually
        // configured to be served over https.
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
