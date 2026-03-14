<?php

declare(strict_types=1);

namespace Oclite\Console;

/**
 * Console Application — register commands and dispatch by argv.
 *
 * Minimal usage:
 *
 *   $app = new Application('MyTool', '1.0.0');
 *   $app->add(new HelloCommand());
 *   $app->run();
 *
 * With a shared context (DI container, registry, etc.):
 *
 *   $app = new Application('artisan', '1.0.0');
 *   $app->setContext($registry);          // passed to every Command::setContext()
 *   $app->addCommands([
 *       new MigrateCommand(),
 *       new SeedCommand(),
 *   ]);
 *   $app->run();
 *
 * Custom argv (useful for embedding / testing):
 *
 *   $app->run(['artisan', 'migrate', '--force']);
 */
class Application
{
    /** @var array<string, Command> */
    private array $commands = [];

    private mixed $context  = null;

    public function __construct(
        private readonly string $name    = 'console',
        private readonly string $version = '1.0.0',
    ) {}

    // ------------------------------------------------------------------ //
    // Registration                                                        //
    // ------------------------------------------------------------------ //

    public function add(Command $command): static
    {
        $this->commands[$command->getName()] = $command;
        return $this;
    }

    /** @param list<Command> $commands */
    public function addCommands(array $commands): static
    {
        foreach ($commands as $command) {
            $this->add($command);
        }
        return $this;
    }

    /**
     * Set the context that will be forwarded to every command via Command::setContext().
     * Use this to pass a DI container, OpenCart registry, PDO instance, etc.
     */
    public function setContext(mixed $context): static
    {
        $this->context = $context;
        return $this;
    }

    // ------------------------------------------------------------------ //
    // Dispatch                                                            //
    // ------------------------------------------------------------------ //

    /**
     * Parse argv and run the matching command.
     *
     * @param list<string>|null $argv  Defaults to global $argv.
     * @return int                     Exit code (Command::SUCCESS / FAILURE / INVALID).
     */
    public function run(?array $argv = null): int
    {
        $argv   = $argv ?? ($_SERVER['argv'] ?? []);
        $input  = new Input($argv);
        $output = new Output();

        $commandName = $input->command();

        if ($commandName === '' || in_array($commandName, ['help', '--help', '-h'], true)) {
            $this->printHelp($output);
            return Command::SUCCESS;
        }

        if (in_array($commandName, ['version', '--version', '-V'], true)) {
            $output->info("{$this->name} {$this->version}");
            return Command::SUCCESS;
        }

        if (!isset($this->commands[$commandName])) {
            $output->error("Command \"{$commandName}\" is not defined.");
            $this->printSuggestions($commandName, $output);
            return Command::INVALID;
        }

        $command = $this->commands[$commandName];
        $command->setInput($input);
        $command->setOutput($output);

        if ($this->context !== null) {
            $command->setContext($this->context);
        }

        try {
            $code = $command->handle();
        } catch (\Throwable $e) {
            $output->error('Error: ' . $e->getMessage());
            $output->comment('  at ' . $e->getFile() . ':' . $e->getLine());
            return Command::FAILURE;
        }

        return $code;
    }

    // ------------------------------------------------------------------ //
    // Help rendering                                                      //
    // ------------------------------------------------------------------ //

    private function printHelp(Output $output): void
    {
        $output->info("{$this->name} {$this->version}");
        $output->newLine();
        $output->writeln('<comment>Usage:</comment>');
        $output->writeln('  command [options] [arguments]');
        $output->newLine();
        $output->writeln('<comment>Available commands:</comment>');

        // Group by namespace (the part before the first colon)
        $grouped = [];
        foreach ($this->commands as $name => $cmd) {
            $ns = str_contains($name, ':') ? explode(':', $name, 2)[0] : '';
            $grouped[$ns][$name] = $cmd;
        }

        ksort($grouped);

        foreach ($grouped as $ns => $cmds) {
            if ($ns !== '') {
                $output->writeln(" <comment>{$ns}</comment>");
            }
            foreach ($cmds as $name => $cmd) {
                $pad = str_pad($name, 30);
                $output->writeln("  <info>{$pad}</info> {$cmd->getDescription()}");
            }
        }
    }

    /** Suggest similar command names using Levenshtein distance. */
    private function printSuggestions(string $name, Output $output): void
    {
        $suggestions = [];
        foreach (array_keys($this->commands) as $cmd) {
            if (levenshtein($name, $cmd) <= 3 || str_starts_with($cmd, $name)) {
                $suggestions[] = $cmd;
            }
        }

        if ($suggestions) {
            $output->newLine();
            $output->writeln('<comment>Did you mean one of these?</comment>');
            foreach ($suggestions as $s) {
                $output->writeln("  <info>{$s}</info>");
            }
        }
    }
}
