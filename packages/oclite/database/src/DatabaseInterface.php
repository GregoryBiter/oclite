<?php

declare(strict_types=1);

namespace Oclite\Database;

/**
 * Minimal interface that the database layer must implement.
 *
 * OCLite ships its own DB class that matches this contract.
 * For tests you can use a mock or a simple PDO adapter.
 */
interface DatabaseInterface
{
    /** Execute a raw SQL string. Returns a result object. */
    public function query(string $sql): mixed;

    /** Return the last auto-increment ID produced by an INSERT. */
    public function getLastId(): int;

    /** Escape a value for safe inclusion in a SQL string. */
    public function escape(string $value): string;
}
