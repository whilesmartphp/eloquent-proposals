<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Whilesmart\Customers\CustomersServiceProvider;
use Whilesmart\OwnerAccess\OwnerAccessServiceProvider;
use Whilesmart\Proposals\ProposalsServiceProvider;
use Whilesmart\Shareables\ShareablesServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../vendor/whilesmart/eloquent-customers/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../vendor/whilesmart/eloquent-shareables/database/migrations');

        Schema::create('workspaces', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            OwnerAccessServiceProvider::class,
            CustomersServiceProvider::class,
            ShareablesServiceProvider::class,
            ProposalsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('proposals.route_middleware', ['api']);
    }
}
