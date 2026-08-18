<?php

namespace Whilesmart\Proposals;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Whilesmart\Proposals\Interfaces\ResponseFormatterInterface;
use Whilesmart\Proposals\Models\Proposal;
use Whilesmart\Proposals\ResponseFormatters\DefaultResponseFormatter;

class ProposalsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/proposals.php', 'proposals');

        $this->app->bind(ResponseFormatterInterface::class, function () {
            return app(config('proposals.response_formatter', DefaultResponseFormatter::class));
        });
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

        Route::bind('proposal', function ($value) {
            return (config('proposals.model', Proposal::class))::query()->findOrFail($value);
        });

        if (config('proposals.register_routes', true)) {
            Route::middleware(config('proposals.route_middleware', ['api', 'auth:sanctum']))
                ->prefix(config('proposals.route_prefix', 'api'))
                ->group(__DIR__.'/../routes/api.php');
        }
    }
}
