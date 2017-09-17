<?php

namespace RodrigoPedra\LaravelSimpleACL;

use Illuminate\Support\ServiceProvider;
use RuntimeException;

class SimpleACLServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $configPath = $this->app[ 'path.config' ] . DIRECTORY_SEPARATOR . 'simple-acl.php';
        $this->publishes( [ __DIR__ . '/../config.php' => $configPath ] );

        $this->loadMigrationsFrom( __DIR__ . '/../migrations' );
    }

    public function register()
    {
        $this->mergeConfigFrom( __DIR__ . '/../config.php', 'simple-acl' );
        $this->setDatabaseConnection();
    }

    private function setDatabaseConnection()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app[ 'config' ];

        $userClassName = $config->get( 'simple-acl.user-class' );

        /** @var \Illuminate\Database\Eloquent\Model $userModel */
        $userModel  = ( new $userClassName );
        $connection = $userModel->getConnectionName();

        $settings = $config->get( "database.connections.{$connection}", null );

        if (is_null( $settings )) {
            throw new RuntimeException( 'Invalid database connection' );
        }

        $config->set( [ 'database.connections.simple-acl' => $settings ] );
    }
}
