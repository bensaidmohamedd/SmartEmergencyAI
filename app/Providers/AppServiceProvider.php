<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Fix MySQL older versions
        Schema::defaultStringLength(191);

        // Force HTTPS in production (fix mixed content)
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}