<?php

declare(strict_types=1);

namespace Oclite\Database;

/**
 * Base class for all migrations.
 *
 * Each migration file must define a class that extends Migration and
 * implements up() (and optionally down()).
 *
 * The class name is derived from the filename by the Runner:
 *   2024_03_14_0001_initial_schema.php  =>  M202403140001InitialSchema
 *   (prefix 'M' + filename-without-extension with underscores removed, CamelCased)
 *
 * Example migration file:
 *
 *   <?php
 *   use Oclite\Database\Migration;
 *
 *   class M202403140001InitialSchema extends Migration
 *   {
 *       public function up(): void
 *       {
 *           $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->prefix}example` (
 *               `id` int(11) NOT NULL AUTO_INCREMENT,
 *               `name` varchar(255) NOT NULL DEFAULT '',
 *               PRIMARY KEY (`id`)
 *           ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
 *       }
 *
 *       public function down(): void
 *       {
 *           $this->db->query("DROP TABLE IF EXISTS `{$this->prefix}example`");
 *       }
 *   }
 */
abstract class Migration
{
    protected DatabaseInterface $db;
    protected string $prefix;

    public function __construct(DatabaseInterface $db, string $prefix = '')
    {
        $this->db     = $db;
        $this->prefix = $prefix;
    }

    /** Apply the migration. */
    abstract public function up(): void;

    /** Reverse the migration (optional). */
    public function down(): void
    {
        // no-op by default; override if you need rollback
    }
}
