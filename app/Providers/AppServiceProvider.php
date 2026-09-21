<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Supaya semua view di dalam subdomain tenant (layouts.tenant & turunannya)
        // otomatis punya $tenant tanpa perlu compact('tenant') di tiap controller.
        View::composer(['tenant.*', 'pages.companies.*', 'pages.employee.*', 'layouts.tenant'], function ($view) {
            if (! $view->offsetExists('tenant') && app()->bound('tenant')) {
                $view->with('tenant', app('tenant'));
            }
        });
    }
}
