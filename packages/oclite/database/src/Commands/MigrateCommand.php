<?php

declare(strict_types=1);

namespace Oclite\Database\Commands;

use Oclite\Console\Command;
use Oclite\Database\Runner;

/**
 * Run all pending migrations.
 *
 *   php artisan migrate
 */
class MigrateCommand extends Command
{
    protected string $name        = 'migrate';
    protected string $description = 'Run the database migrations';
    protected string $help        = <<<'HELP'
Run all pending migration files located in the configured migrations path.

Options:
  (none)

Examples:
  php artisan migrate
HELP;

    public function handle(): int
    {
        $runner = $this->runner();
        $runner->migrate();
        return self::SUCCESS;
    }

    protected function runner(): Runner
    {
        return $this->context;
    }
}
