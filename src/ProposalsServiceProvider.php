<?php

namespace Whilesmart\Proposals;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ProposalsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/proposals.php', 'proposals');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/proposals.php' => config_path('proposals.php'),
        ], 'proposals-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'proposals-migrations');

        if (config('proposals.register_routes', true)) {
            Route::middleware(config('proposals.route_middleware', ['api', 'auth:sanctum']))
                ->prefix(config('proposals.route_prefix', 'api'))
                ->group(__DIR__.'/../routes/api.php');
        }
    }
}
