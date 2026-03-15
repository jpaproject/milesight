<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;


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
        $proxy_url    = getenv('PROXY_URL');
        $proxy_schema = getenv('PROXY_SCHEMA');

        if (!empty($proxy_url)) {
            \URL::forceRootUrl($proxy_url);
        }

        if (!empty($proxy_schema)) {
            \URL::forceScheme($proxy_schema);
        }

        if (!app()->runningInConsole() && Schema::hasTable('terminals')) {
            $terminals = \App\Models\Terminal::all();
            view()->share('terminals', $terminals);
        }
    }
}
