<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // If APP_URL is set to https:// and not accessed via local dev server, force HTTPS
        if (str_starts_with(config('app.url'), 'https://') && !in_array(request()->getHost(), ['127.0.0.1', 'localhost', '::1'])) {
            URL::forceScheme('https');
        }
    }
}
