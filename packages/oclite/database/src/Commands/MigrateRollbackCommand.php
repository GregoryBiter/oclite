<?php

declare(strict_types=1);

namespace Oclite\Database\Commands;

use Oclite\Console\Command;

/**
 * Roll back the last batch of migrations.
 *
 *   php artisan migrate:rollback
 *   php artisan migrate:rollback --step=3
 */
class MigrateRollbackCommand extends Command
{
    protected string $name        = 'migrate:rollback';
    protected string $description = 'Rollback the last database migration batch';
    protected string $help        = <<<'HELP'
Roll back the most recently applied batch of migrations.

Options:
  --step=N   Number of batches to roll back (default: 1)

Examples:
  php artisan migrate:rollback
  php artisan migrate:rollback --step=2
HELP;

    public function handle(): int
    {
        $steps = (int)($this->option('step', '1') ?: 1);
        $this->context->rollback($steps);
        return self::SUCCESS;
    }
}
