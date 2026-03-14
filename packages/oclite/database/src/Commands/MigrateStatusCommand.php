<?php

declare(strict_types=1);

namespace Oclite\Database\Commands;

use Oclite\Console\Command;

/**
 * Show the status of each migration.
 *
 *   php artisan migrate:status
 */
class MigrateStatusCommand extends Command
{
    protected string $name        = 'migrate:status';
    protected string $description = 'Show the status of each migration';

    public function handle(): int
    {
        $applied = $this->context->appliedMigrations();

        if (!$applied) {
            $this->comment('No migrations have been run yet.');
            return self::SUCCESS;
        }

        $rows = array_map(fn($row) => [
            (string)($row['name'] ?? ''),
            (string)($row['batch'] ?? ''),
        ], $applied);

        $this->output->table(['Migration', 'Batch'], $rows);

        return self::SUCCESS;
    }
}
