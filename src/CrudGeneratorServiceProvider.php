<?php

namespace NamaaSolutions\CrudGenerator;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Providers\ConsoleServiceProvider;

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
        //
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
}
