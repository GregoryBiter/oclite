<?php

declare(strict_types=1);

namespace Oclite\Database\Commands;

use Oclite\Console\Command;

/**
 * Scaffold a new seeder file.
 *
 *   php artisan make:seeder UserSeeder
 */
class MakeSeederCommand extends Command
{
    protected string $name        = 'make:seeder';
    protected string $description = 'Create a new seeder file';
    protected string $help        = <<<'HELP'
Generate a new seeder file in the seeds directory.

Arguments:
  name   Class name, e.g. "UserSeeder"

Options:
  --path=DIR   Override seeds directory

Examples:
  php artisan make:seeder UserSeeder
HELP;

    public function handle(): int
    {
        $name = $this->argument(0);

        if ($name === '') {
            $this->error('Please provide a seeder name.');
            return self::INVALID;
        }

        $seedsPath = $this->resolveSeedsPath();

        if (!is_dir($seedsPath) && !mkdir($seedsPath, 0755, true)) {
            $this->error("Could not create directory: {$seedsPath}");
            return self::FAILURE;
        }

        $timestamp = date('Y_m_d');
        $existing  = glob($seedsPath . DIRECTORY_SEPARATOR . '*.php') ?: [];
        $seq       = str_pad((string)(count($existing) + 1), 4, '0', STR_PAD_LEFT);
        $filename  = "{$timestamp}_{$seq}_{$name}.php";
        // Class name: S + timestamp + seq + name
        $className = 'S' . str_replace(' ', '', ucwords(str_replace('_', ' ', "{$timestamp}_{$seq}_{$name}")));

        $stub = $this->stub($className);
        file_put_contents($seedsPath . DIRECTORY_SEPARATOR . $filename, $stub);

        $this->info("Created seeder: {$filename}");

        return self::SUCCESS;
    }

    private function resolveSeedsPath(): string
    {
        if (is_object($this->context) && method_exists($this->context, 'getSeedsPath')) {
            return $this->context->getSeedsPath();
        }

        $override = (string)$this->option('path', '');
        return $override !== '' ? $override : getcwd() . '/database/seeds';
    }

    private function stub(string $className): string
    {
        return <<<PHP
<?php

use Oclite\Database\Seeder;

class {$className} extends Seeder
{
    public function run(): void
    {
        // \$this->db->query("INSERT INTO `{\$this->prefix}example` SET `name` = 'value'");
    }
}
PHP;
    }
}
