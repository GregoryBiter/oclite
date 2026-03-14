<?php

declare(strict_types=1);

namespace Oclite\Console;

/**
 * Thin wrapper around STDOUT / STDERR with ANSI colour helpers.
 *
 * Colours:
 *   $output->writeln('<info>Done!</info>');
 *   $output->writeln('<error>Oops</error>');
 *   $output->writeln('<comment>// note</comment>');
 *   $output->writeln('<question>Continue?</question>');
 *   $output->writeln('<warning>Watch out</warning>');
 *
 * Or use the shorthand methods:
 *   $output->info('Done!');
 *   $output->error('Oops');
 *   $output->comment('// note');
 */
class Output
{
    private bool $decorated;

    public function __construct(bool $decorated = true)
    {
        // Auto-detect TTY: if stdout is not a terminal, strip colours
        $this->decorated = $decorated && function_exists('posix_isatty') && posix_isatty(STDOUT);
    }

    // ------------------------------------------------------------------ //
    // Core write methods                                                   //
    // ------------------------------------------------------------------ //

    public function write(string $message): void
    {
        echo $this->format($message);
    }

    public function writeln(string $message = ''): void
    {
        echo $this->format($message) . "\n";
    }

    /** Write to STDERR without colour processing. */
    public function writeError(string $message): void
    {
        fwrite(STDERR, $message . "\n");
    }

    // ------------------------------------------------------------------ //
    // Shorthand helpers                                                    //
    // ------------------------------------------------------------------ //

    public function info(string $message): void
    {
        $this->writeln("<info>{$message}</info>");
    }

    public function error(string $message): void
    {
        $this->writeln("<error>{$message}</error>");
    }

    public function comment(string $message): void
    {
        $this->writeln("<comment>{$message}</comment>");
    }

    public function warning(string $message): void
    {
        $this->writeln("<warning>{$message}</warning>");
    }

    public function question(string $message): void
    {
        $this->writeln("<question>{$message}</question>");
    }

    public function newLine(int $count = 1): void
    {
        echo str_repeat("\n", $count);
    }

    // ------------------------------------------------------------------ //
    // Table helper                                                         //
    // ------------------------------------------------------------------ //

    /**
     * Render a simple ASCII table.
     *
     * @param list<string>         $headers
     * @param list<list<string>>   $rows
     */
    public function table(array $headers, array $rows): void
    {
        $cols = count($headers);
        $widths = array_map('strlen', $headers);

        foreach ($rows as $row) {
            for ($i = 0; $i < $cols; $i++) {
                $widths[$i] = max($widths[$i], strlen((string)($row[$i] ?? '')));
            }
        }

        $line = '+' . implode('+', array_map(fn($w) => str_repeat('-', $w + 2), $widths)) . '+';

        $this->writeln($line);
        $header = '|';
        foreach ($headers as $i => $h) {
            $header .= ' ' . str_pad($h, $widths[$i]) . ' |';
        }
        $this->writeln("<info>{$header}</info>");
        $this->writeln($line);

        foreach ($rows as $row) {
            $r = '|';
            for ($i = 0; $i < $cols; $i++) {
                $r .= ' ' . str_pad((string)($row[$i] ?? ''), $widths[$i]) . ' |';
            }
            $this->writeln($r);
        }

        $this->writeln($line);
    }

    // ------------------------------------------------------------------ //
    // ANSI formatting                                                      //
    // ------------------------------------------------------------------ //

    private const TAGS = [
        'info'     => ["\033[32m", "\033[0m"],  // green
        'error'    => ["\033[31m", "\033[0m"],  // red
        'comment'  => ["\033[33m", "\033[0m"],  // yellow
        'warning'  => ["\033[33;1m", "\033[0m"],// bright yellow
        'question' => ["\033[36m", "\033[0m"],  // cyan
    ];

    private function format(string $message): string
    {
        if (!$this->decorated) {
            return preg_replace('/<\/?(?:' . implode('|', array_keys(self::TAGS)) . ')>/i', '', $message) ?? $message;
        }

        foreach (self::TAGS as $tag => [$open, $close]) {
            $message = str_replace(
                ["<{$tag}>", "</{$tag}>"],
                [$open, $close],
                $message
            );
        }

        return $message;
    }
}
