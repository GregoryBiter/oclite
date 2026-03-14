<?php

declare(strict_types=1);

namespace Oclite\Database\Commands;

use Oclite\Console\Command;

/**
 * Drop all tables and re-run all migrations from scratch.
 *
 *   php artisan migrate:fresh
 *   php artisan migrate:fresh --seed      # also run all seeders afterwards
 */
class MigrateFreshCommand extends Command
{
    protected string $name        = 'migrate:fresh';
    protected string $description = 'Drop all tables and re-run all migrations';
    protected string $help        = <<<'HELP'
Drop ALL database tables, then run every migration file from the beginning.

Options:
  --seed    Also run all seeders after migration

Examples:
  php artisan migrate:fresh
  php artisan migrate:fresh --seed
HELP;

    public function handle(): int
    {
        if (!$this->hasOption('force') && !$this->confirm('This will drop ALL tables. Continue?')) {
            $this->warning('Cancelled.');
            return self::SUCCESS;
        }

        $runner = $this->context;
        $runner->fresh();

        if ($this->hasOption('seed')) {
            $this->line();
            $runner->seed();
        }

        return self::SUCCESS;
    }
}
