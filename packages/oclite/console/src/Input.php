<?php

declare(strict_types=1);

namespace Oclite\Console;

/**
 * Parses a raw $argv array into positional arguments and named options.
 *
 * Usage:
 *   php artisan migrate --force --db=mysql --connection=main subarg
 *
 * After parse():
 *   $input->command()        => 'migrate'
 *   $input->argument(0)      => 'subarg'
 *   $input->option('force')  => true
 *   $input->option('db')     => 'mysql'
 *   $input->option('connection') => 'main'
 */
class Input
{
    private string $command   = '';
    /** @var list<string> */
    private array $arguments  = [];
    /** @var array<string, string|bool> */
    private array $options    = [];

    /** @param list<string> $argv Raw $argv (index 0 = script, index 1 = command) */
    public function __construct(private readonly array $argv = [])
    {
        $this->parse();
    }

    private function parse(): void
    {
        $tokens = array_slice($this->argv, 1); // skip script name

        if ($tokens && !str_starts_with($tokens[0], '-')) {
            $this->command = array_shift($tokens);
        }

        foreach ($tokens as $token) {
            if (str_starts_with($token, '--')) {
                $token = substr($token, 2);
                if (str_contains($token, '=')) {
                    [$key, $val] = explode('=', $token, 2);
                    $this->options[$key] = $val;
                } else {
                    $this->options[$token] = true;
                }
            } elseif (str_starts_with($token, '-') && strlen($token) === 2) {
                $this->options[substr($token, 1)] = true;
            } else {
                $this->arguments[] = $token;
            }
        }
    }

    public function command(): string
    {
        return $this->command;
    }

    /** Return positional arg by zero-based index, or $default if missing. */
    public function argument(int $index, string $default = ''): string
    {
        return $this->arguments[$index] ?? $default;
    }

    /** @return list<string> */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /** @return string|bool|null */
    public function option(string $name, string|bool|null $default = null): string|bool|null
    {
        return $this->options[$name] ?? $default;
    }

    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    /** @return array<string, string|bool> */
    public function options(): array
    {
        return $this->options;
    }
}
