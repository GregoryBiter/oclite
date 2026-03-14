<?php

declare(strict_types=1);

namespace Oclite\Database\Commands;

use Oclite\Console\Command;

/**
 * Scaffold a new factory file.
 *
 *   php artisan make:factory UserFactory
 */
class MakeFactoryCommand extends Command
{
    protected string $name        = 'make:factory';
    protected string $description = 'Create a new model factory file';
    protected string $help        = <<<'HELP'
Generate a new factory file in the factories directory.

Arguments:
  name   Class name, e.g. "UserFactory"

Options:
  --path=DIR   Override factories directory

Examples:
  php artisan make:factory UserFactory
HELP;

    public function handle(): int
    {
        $name = $this->argument(0);

        if ($name === '') {
            $this->error('Please provide a factory name.');
            return self::INVALID;
        }

        $factoriesPath = $this->resolveFactoriesPath();

        if (!is_dir($factoriesPath) && !mkdir($factoriesPath, 0755, true)) {
            $this->error("Could not create directory: {$factoriesPath}");
            return self::FAILURE;
        }

        $filename = "{$name}.php";
        $filepath = $factoriesPath . DIRECTORY_SEPARATOR . $filename;

        if (is_file($filepath)) {
            $this->warning("Factory already exists: {$filename}");
            return self::INVALID;
        }

        file_put_contents($filepath, $this->stub($name));
        $this->info("Created factory: {$filename}");

        return self::SUCCESS;
    }

    private function resolveFactoriesPath(): string
    {
        if (is_object($this->context) && method_exists($this->context, 'getFactoriesPath')) {
            return $this->context->getFactoriesPath();
        }

        $override = (string)$this->option('path', '');
        return $override !== '' ? $override : getcwd() . '/database/factories';
    }

    private function stub(string $className): string
    {
        return <<<PHP
<?php

use Oclite\Database\Factory;

class {$className} extends Factory
{
    public function definition(): array
    {
        return [
            // 'column' => 'value_' . \$this->sequence(),
        ];
    }
}
PHP;
    }
}
