<?php

namespace Uatthaphon\ActivityMonitor;

use Illuminate\Support\ServiceProvider;
use Uatthaphon\ActivityMonitor\ActivityMonitor;

class ActivityMonitorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ActivityMonitor'];
    }
}
