<?php

declare(strict_types=1);

namespace Oclite\Database\Commands;

use Oclite\Console\Command;

/**
 * Seed the database.
 *
 *   php artisan db:seed
 */
class SeedCommand extends Command
{
    protected string $name        = 'db:seed';
    protected string $description = 'Seed the database with records';
    protected string $help        = <<<'HELP'
Run all seeder files located in the configured seeds path.

Examples:
  php artisan db:seed
HELP;

    public function handle(): int
    {
        $this->context->seed();
        return self::SUCCESS;
    }
}
