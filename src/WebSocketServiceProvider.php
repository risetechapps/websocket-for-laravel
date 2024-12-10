<?php

namespace RiseTechApps\WebSocket;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RiseTechApps\Address\Events\Address\AddressCreateOrUpdateBillingEvent;
use RiseTechApps\Address\Events\Address\AddressCreateOrUpdateDefaultEvent;
use RiseTechApps\Address\Events\Address\AddressCreateOrUpdateDeliveryEvent;

class WebSocketServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Register the main class to use with the facade
        $this->app->singleton('websocket', function () {
            return new WebSocket;
        });
    }
}
