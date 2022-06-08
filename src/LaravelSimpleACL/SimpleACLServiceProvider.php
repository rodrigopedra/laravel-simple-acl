<?php

namespace RodrigoPedra\LaravelSimpleACL;

use Illuminate\Support\ServiceProvider;
use RuntimeException;

class SimpleACLServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = $this->app['path.config'] . DIRECTORY_SEPARATOR . 'simple-acl.php';
        $this->publishes([__DIR__ . '/../../config/simple-acl.php' => $configPath]);

        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/simple-acl.php', 'simple-acl');
        $this->setDatabaseConnection();
    }

    private function setDatabaseConnection()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];

        $connection = $config->get('simple-acl.db-connection') ?: $config->get('database.default');
        $settings = $config->get("database.connections.{$connection}", null);

        if (is_null($settings)) {
            throw new RuntimeException('Invalid database connection');
        }

        $config->set([
            'simple-acl.db-connection' => $connection,
            'database.connections.simple-acl' => $settings,
        ]);
    }
}
