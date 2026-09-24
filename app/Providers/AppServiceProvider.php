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
        // Supaya semua view di dalam subdomain tenant (layouts.company, pages.company, pages.employee, pages.status & turunannya)
        // otomatis punya $tenant tanpa perlu compact('tenant') di tiap controller.
        View::composer(['tenant.*', 'pages.status.*', 'pages.company.*', 'pages.employee.*', 'layouts.company', '_legacy.*'], function ($view) {
            if (! $view->offsetExists('tenant') && app()->bound('tenant')) {
                $view->with('tenant', app('tenant'));
            }
        });
    }
}
