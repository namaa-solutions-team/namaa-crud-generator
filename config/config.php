<?php

use NamaaSolutions\CrudGenerator\Commands;

return [
    /*
    |--------------------------------------------------------------------------
    | Package commands
    |--------------------------------------------------------------------------
    |
    */
    'commands' => [
        Commands\ModelMakeCrudCommand::class,
        Commands\MigrationMakeCrudCommand::class,
        Commands\ControllerMakeCommand::class,
    ],

];
