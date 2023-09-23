<?php

namespace NamaaSolutions\CrudGenerator;

use Illuminate\Support\ServiceProvider;
use NamaaSolutions\CrudGenerator\Providers\ConsoleServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * Used to initialize some routes or add an event listener
     * @return void
     */
    public function register()
    {
        $this->registerProviders();
        $this->registerNamespaces();
    }

    /**
     * Bootstrap services.
     *
     * Used to bind our package to the classes inside the app container
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__ . '/../config/config.php';
        // $stubsPath = dirname(__DIR__) . '/src/Commands/stubs';

        $this->publishes([
            $configPath => config_path('namaa-crud.php'),
        ], 'config');

        // $this->publishes([
        //     $stubsPath => base_path('stubs/namaa-stubs'),
        // ], 'stubs');
    }
}
