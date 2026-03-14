<?php

declare(strict_types=1);

namespace Oclite\Database;

/**
 * Base class for model factories.
 *
 * Factories generate fake rows for testing / seeding.
 *
 * Example:
 *
 *   <?php
 *   use Oclite\Database\Factory;
 *
 *   class UserFactory extends Factory
 *   {
 *       public function definition(): array
 *       {
 *           return [
 *               'username'   => 'user_' . $this->sequence(),
 *               'email'      => 'user' . $this->sequence() . '@example.com',
 *               'status'     => 1,
 *               'date_added' => date('Y-m-d H:i:s'),
 *           ];
 *       }
 *   }
 *
 *   // Usage:
 *   $factory = new UserFactory($db, 'oc_');
 *   $rows = $factory->count(5)->make();               // array of rows
 *   $factory->count(5)->create('user');               // INSERT into oc_user
 */
abstract class Factory
{
    protected DatabaseInterface $db;
    protected string   $prefix;
    private int        $count    = 1;
    private int        $seq      = 0;

    public function __construct(DatabaseInterface $db, string $prefix = '')
    {
        $this->db     = $db;
        $this->prefix = $prefix;
    }

    /** Define the default column values for a single row. */
    abstract public function definition(): array;

    // ------------------------------------------------------------------ //
    // Builder fluent API                                                  //
    // ------------------------------------------------------------------ //

    public function count(int $count): static
    {
        $clone        = clone $this;
        $clone->count = max(1, $count);
        $clone->seq   = $this->seq;
        return $clone;
    }

    // ------------------------------------------------------------------ //
    // Generation                                                          //
    // ------------------------------------------------------------------ //

    /**
     * Return an array of row arrays without persisting them.
     *
     * @param array<string, mixed> $overrides  Override specific columns.
     * @return list<array<string, mixed>>
     */
    public function make(array $overrides = []): array
    {
        $rows = [];
        for ($i = 0; $i < $this->count; $i++) {
            $this->seq++;
            $rows[] = array_merge($this->definition(), $overrides);
        }
        return $rows;
    }

    /**
     * Generate rows and INSERT them into the given table.
     *
     * @param string               $table      Table name WITHOUT prefix.
     * @param array<string, mixed> $overrides  Override specific columns.
     * @return list<int>                        Last-insert IDs for each row.
     */
    public function create(string $table, array $overrides = []): array
    {
        $ids  = [];
        $rows = $this->make($overrides);

        foreach ($rows as $row) {
            [$sql] = $this->buildInsert($table, $row);
            $this->db->query($sql);
            $ids[] = $this->db->getLastId();
        }

        return $ids;
    }

    // ------------------------------------------------------------------ //
    // Helpers                                                             //
    // ------------------------------------------------------------------ //

    /** Auto-incrementing counter starting at 1 within this factory call. */
    protected function sequence(): int
    {
        return $this->seq;
    }

    /** @return array{0: string} */
    private function buildInsert(string $table, array $row): array
    {
        $sets = [];
        foreach ($row as $col => $val) {
            $escaped = $this->db->escape((string)$val);
            $sets[]  = "`{$col}` = '{$escaped}'";
        }

        $sql = "INSERT INTO `{$this->prefix}{$table}` SET " . implode(', ', $sets);
        return [$sql];
    }
}
