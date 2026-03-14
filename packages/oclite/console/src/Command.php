<?php

declare(strict_types=1);

namespace Oclite\Console;

/**
 * Base class for all console commands.
 *
 * Extend this class, define $name / $description / $help, implement handle(),
 * and register the command in the Application.
 *
 * Example:
 *
 *   class HelloCommand extends Command
 *   {
 *       protected string $name        = 'hello';
 *       protected string $description = 'Say hello';
 *
 *       public function handle(): int
 *       {
 *           $name = $this->input->argument(0, 'World');
 *           $this->output->info("Hello, {$name}!");
 *           return Command::SUCCESS;
 *       }
 *   }
 *
 *   // Run via:
 *   //   php console.php hello Alice
 */
abstract class Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;

    protected string $name        = '';
    protected string $description = '';
    protected string $help        = '';

    protected Input  $input;
    protected Output $output;

    /**
     * The framework might inject arbitrary context (e.g. a DI container,
     * database connection, registry) via this property.
     *
     * @var mixed
     */
    protected mixed $context = null;

    public function setInput(Input $input): void
    {
        $this->input = $input;
    }

    public function setOutput(Output $output): void
    {
        $this->output = $output;
    }

    /**
     * Pass any application-level context to the command.
     * What "context" means depends entirely on the host application.
     */
    public function setContext(mixed $context): void
    {
        $this->context = $context;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getHelp(): string
    {
        return $this->help ?: $this->description;
    }

    /**
     * Implement the command logic here.
     *
     * Return one of Command::SUCCESS / Command::FAILURE / Command::INVALID.
     */
    abstract public function handle(): int;

    // ------------------------------------------------------------------ //
    // Convenience helpers (delegate to $output / $input)                  //
    // ------------------------------------------------------------------ //

    protected function info(string $message): void
    {
        $this->output->info($message);
    }

    protected function error(string $message): void
    {
        $this->output->error($message);
    }

    protected function comment(string $message): void
    {
        $this->output->comment($message);
    }

    protected function warning(string $message): void
    {
        $this->output->warning($message);
    }

    protected function line(string $message = ''): void
    {
        $this->output->writeln($message);
    }

    protected function argument(int $index, string $default = ''): string
    {
        return $this->input->argument($index, $default);
    }

    protected function option(string $name, string|bool|null $default = null): string|bool|null
    {
        return $this->input->option($name, $default);
    }

    protected function hasOption(string $name): bool
    {
        return $this->input->hasOption($name);
    }

    /**
     * Ask a yes/no question. Returns true if user confirmed.
     * Falls back to $default when input is not a TTY.
     */
    protected function confirm(string $question, bool $default = false): bool
    {
        $hint = $default ? 'Y/n' : 'y/N';
        echo $this->formatQuestion("{$question} [{$hint}]: ");

        if (!function_exists('posix_isatty') || !posix_isatty(STDIN)) {
            echo ($default ? 'yes' : 'no') . " (non-interactive)\n";
            return $default;
        }

        $answer = trim((string)fgets(STDIN));

        if ($answer === '') {
            return $default;
        }

        return in_array(strtolower($answer), ['y', 'yes'], true);
    }

    /**
     * Ask for a free-form string value.
     */
    protected function ask(string $question, string $default = ''): string
    {
        echo $this->formatQuestion($question . ($default !== '' ? " [{$default}]" : '') . ': ');

        if (!function_exists('posix_isatty') || !posix_isatty(STDIN)) {
            echo "{$default} (non-interactive)\n";
            return $default;
        }

        $answer = trim((string)fgets(STDIN));

        return $answer !== '' ? $answer : $default;
    }

    private function formatQuestion(string $text): string
    {
        return "\033[36m{$text}\033[0m";
    }
}
