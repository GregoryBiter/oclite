<?php

declare(strict_types=1);

namespace Oclite\Database\Commands;

use Oclite\Console\Command;

/**
 * Scaffold a new migration file.
 *
 *   php artisan make:migration create_orders_table
 *   php artisan make:migration add_status_to_users
 */
class MakeMigrationCommand extends Command
{
    protected string $name        = 'make:migration';
    protected string $description = 'Create a new migration file';
    protected string $help        = <<<'HELP'
Generate a new migration file in the migrations directory.

Arguments:
  name   Descriptive name, e.g. "create_orders_table"

Options:
  --path=DIR   Override migrations directory

Examples:
  php artisan make:migration create_orders_table
HELP;

    public function handle(): int
    {
        $name = $this->argument(0);

        if ($name === '') {
            $this->error('Please provide a migration name.');
            return self::INVALID;
        }

        [$migrationsPath] = $this->resolvePaths();

        if (!is_dir($migrationsPath) && !mkdir($migrationsPath, 0755, true)) {
            $this->error("Could not create directory: {$migrationsPath}");
            return self::FAILURE;
        }

        $timestamp = date('Y_m_d');
        $existing  = glob($migrationsPath . DIRECTORY_SEPARATOR . '*.php') ?: [];
        $seq       = str_pad((string)(count($existing) + 1), 4, '0', STR_PAD_LEFT);
        $filename  = "{$timestamp}_{$seq}_{$name}.php";
        $className = 'M' . str_replace(' ', '', ucwords(str_replace('_', ' ', "{$timestamp}_{$seq}_{$name}")));

        $stub = $this->stub($className);
        file_put_contents($migrationsPath . DIRECTORY_SEPARATOR . $filename, $stub);

        $this->info("Created migration: {$filename}");

        return self::SUCCESS;
    }

    private function resolvePaths(): array
    {
        // The context may expose paths; fall back to sensible defaults.
        if (is_object($this->context) && method_exists($this->context, 'getMigrationsPath')) {
            return [$this->context->getMigrationsPath()];
        }

        $override = (string)$this->option('path', '');
        return [$override !== '' ? $override : getcwd() . '/database/migrations'];
    }

    private function stub(string $className): string
    {
        return <<<PHP
<?php

use Oclite\Database\Migration;

class {$className} extends Migration
{
    public function up(): void
    {
        // \$this->db->query("CREATE TABLE IF NOT EXISTS `{\$this->prefix}example` (
        //   `id` int(11) NOT NULL AUTO_INCREMENT,
        //   `name` varchar(255) NOT NULL DEFAULT '',
        //   PRIMARY KEY (`id`)
        // ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(): void
    {
        // \$this->db->query("DROP TABLE IF EXISTS `{\$this->prefix}example`");
    }
}
PHP;
    }
}
