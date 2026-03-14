<?php

declare(strict_types=1);

namespace Oclite\Database;

use Oclite\Console\Application;
use Oclite\Database\Commands\MakeFactoryCommand;
use Oclite\Database\Commands\MakeMigrationCommand;
use Oclite\Database\Commands\MakeSeederCommand;
use Oclite\Database\Commands\MigrateFreshCommand;
use Oclite\Database\Commands\MigrateCommand;
use Oclite\Database\Commands\MigrateRollbackCommand;
use Oclite\Database\Commands\MigrateStatusCommand;
use Oclite\Database\Commands\SeedCommand;

/**
 * Wires the Runner into a Console Application.
 *
 * Usage:
 *
 *   $runner = new Runner($db, 'oc_', '/path/to/migrations', '/path/to/seeds', $output);
 *   DatabaseServiceProvider::register($app, $runner);
 *   $app->run();
 */
class DatabaseServiceProvider
{
    public static function register(Application $app, Runner $runner): void
    {
        $app->setContext($runner);

        $app->addCommands([
            new MigrateCommand(),
            new MigrateFreshCommand(),
            new MigrateRollbackCommand(),
            new MigrateStatusCommand(),
            new SeedCommand(),
            new MakeMigrationCommand(),
            new MakeSeederCommand(),
            new MakeFactoryCommand(),
        ]);
    }
}
