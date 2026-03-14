<?php

declare(strict_types=1);

namespace Oclite\Database;

/**
 * Base class for all seeders.
 *
 * Class name convention: filename-without-extension with underscores removed,
 * prefix 'S':
 *   2024_03_14_0001_initial_data.php  =>  S202403140001InitialData
 *
 * Example:
 *
 *   <?php
 *   use Oclite\Database\Seeder;
 *
 *   class S202403140001InitialData extends Seeder
 *   {
 *       public function run(): void
 *       {
 *           $this->db->query("INSERT INTO `{$this->prefix}language` SET ...");
 *       }
 *   }
 */
abstract class Seeder
{
    protected DatabaseInterface $db;
    protected string $prefix;

    public function __construct(DatabaseInterface $db, string $prefix = '')
    {
        $this->db     = $db;
        $this->prefix = $prefix;
    }

    /** Execute the seeder. */
    abstract public function run(): void;
}
