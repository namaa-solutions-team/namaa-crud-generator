<?php

namespace NamaaSolutions\CrudGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use NamaaSolutions\CrudGenerator\Commands;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     * @var array
     */
    protected $commands = [
        Commands\ModelMakeCrudCommand::class,
    ];

    public function register(): void
    {
        $this->commands(config('modules.commands', $this->commands));
    }

    public function provides(): array
    {
        return $this->commands;
    }
}
