<?php

namespace RodrigoPedra\LaravelSimpleACL;

use Illuminate\Support\ServiceProvider;

class SimpleACLServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->setDatabaseConnection();

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/simple-acl.php' => $this->app->configPath('simple-acl.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/simple-acl.php', 'simple-acl');
    }

    private function setDatabaseConnection(): void
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];

        $connection = $config->get('simple-acl.db-connection') ?? $config->get('database.default');
        $settings = $config->get('database.connections.' . $connection);

        if (\is_null($settings)) {
            throw new \RuntimeException('Invalid database connection');
        }

        $config->set([
            'simple-acl.db-connection' => $connection,
            'database.connections.simple-acl' => $settings,
        ]);
    }
}
