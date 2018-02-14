<?php

namespace Uatthaphon\ActivityMonitor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Uatthaphon\ActivityMonitor\Transformers\ActivityMonitorViewTransformer;
use Uatthaphon\ActivityMonitor\Transformers\ActivityMonitorViewTransformerInterface;

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
        /**
         * publish migrations
         */
        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations')
        ], 'migrations');

        /**
         * Register Facade
         */
        $this->app->bind('ActivityMonitorLog', ActivityMonitorLog::class);
        $this->app->bind('ActivityMonitorView', ActivityMonitorView::class);

        /**
         * Register Service Provider Interface
         */
        $this->app->bind(
            ActivityMonitorViewTransformerInterface::class,
            ActivityMonitorViewTransformer::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ActivityMonitorLog', 'ActivityMonitorView'];
    }
}
