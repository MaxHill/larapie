<?php

namespace Maxhill\Api;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->loadRoutes();
        $this->publishMigrations();
        $this->createDirectories();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->setupJWTAuth();
        $this->setupCorsHeaders();


    }

    private function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('api.php')
        ]);
    }

    private function loadRoutes()
    {
        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/Http/routes.php';
        }
    }

    private function setupJWTAuth()
    {
        // Register provider
        $this->app->register('Tymon\JWTAuth\Providers\JWTAuthServiceProvider');
        // Register aliases
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('JWTAuth', 'Tymon\JWTAuth\Facades\JWTAuth');
        $loader->alias('JWTFactory', 'Tymon\JWTAuth\Facades\JWTFactory');
    }

    private function publishMigrations()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ]);
    }

    private function createDirectories()
    {
        $path = app_path('Transformers');
        if (file_exists($path)) {
            return;
        }
        File::makeDirectory($path);
    }

    /**
     * Fixes CORS and pre-flight requests
     */
    private function setupCorsHeaders()
    {
        if ($this->isOptionsRequest() && app('env') !== 'testing') {
            header('Access-Control-Allow-Origin : *');
            header('Access-Control-Allow-Methods : POST, GET, OPTIONS, PUT, DELETE');
            header('Access-Control-Allow-Headers : Authorization, X-Requested-With, content-type, Cache-Control');
        } else {
            if (app('env') !== 'testing') {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Headers: Authorization, Content-Type, Cache-Control');
            }
        }
    }

    /**
     * @return bool
     */
    private function isOptionsRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) &&
        $_SERVER['REQUEST_METHOD'] == 'OPTIONS';
    }
}
