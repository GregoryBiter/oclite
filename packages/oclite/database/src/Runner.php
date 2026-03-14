<?php

declare(strict_types=1);

namespace Oclite\Database;

use Oclite\Console\Output;

/**
 * Executes migrations and seeders by scanning a directory for PHP files
 * and instantiating the corresponding class.
 *
 * Class-name convention:
 *   migrations/ -> prefix  'M' + name without underscores (CamelCased parts)
 *   seeds/      -> prefix  'S' + name without underscores (CamelCased parts)
 *
 *   File: 2024_03_14_0001_initial_schema.php
 *   Class: M202403140001InitialSchema
 *
 * The Runner keeps an oc_migration tracking table so that already-applied
 * migrations are skipped on subsequent runs.
 */
class Runner
{
    private const MIGRATION_TABLE = 'migration';

    public function __construct(
        private readonly DatabaseInterface $db,
        private readonly string            $prefix,
        private readonly string            $migrationsPath,
        private readonly string            $seedsPath,
        private readonly Output            $output,
        private string                     $factoriesPath = '',
    ) {}

    // ------------------------------------------------------------------ //
    // Path accessors (used by make:* commands via context)               //
    // ------------------------------------------------------------------ //

    public function getMigrationsPath(): string { return $this->migrationsPath; }
    public function getSeedsPath(): string      { return $this->seedsPath; }
    public function getFactoriesPath(): string  { return $this->factoriesPath; }
    public function setFactoriesPath(string $path): void { $this->factoriesPath = $path; }

    // ------------------------------------------------------------------ //
    // Public API                                                          //
    // ------------------------------------------------------------------ //

    /** Run all pending migrations (skip already-applied ones). */
    public function migrate(): void
    {
        $this->ensureMigrationTable();

        foreach ($this->pendingFiles($this->migrationsPath) as $file) {
            $name = basename($file, '.php');
            $this->output->info("Migrating: {$name}");

            $object = $this->loadClass($file, 'M');
            $object->up();

            $this->db->query(
                "INSERT INTO `{$this->prefix}" . self::MIGRATION_TABLE . "` SET `name` = '"
                . $this->db->escape($name) . "', `batch` = " . $this->currentBatch()
            );
        }

        $this->output->info('Migrations complete.');
    }

    /** Drop ALL tables, then run all migrations from scratch. */
    public function fresh(): void
    {
        $this->output->warning('Dropping all tables…');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        $result = $this->db->query('SHOW TABLES');
        if ($result && is_iterable($rows = $this->extractRows($result))) {
            foreach ($rows as $row) {
                $tableName = array_values((array)$row)[0];
                $this->db->query("DROP TABLE IF EXISTS `{$tableName}`");
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        $this->output->info('Done. Running migrations…');
        $this->migrate();
    }

    /** Run all seeders in order. */
    public function seed(): void
    {
        $files = $this->globSorted($this->seedsPath);

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $this->output->info("Seeding: {$name}");

            $object = $this->loadClass($file, 'S');
            $object->run();
        }

        $this->output->info('Seeding complete.');
    }

    /**
     * Roll back the last batch of migrations.
     *
     * @param int $steps  Number of batches to roll back (default 1).
     */
    public function rollback(int $steps = 1): void
    {
        $this->ensureMigrationTable();

        $maxBatch = $this->currentBatch() - 1;
        $minBatch = max(1, $maxBatch - $steps + 1);

        $result = $this->db->query(
            "SELECT `name`, `batch` FROM `{$this->prefix}" . self::MIGRATION_TABLE
            . "` WHERE `batch` >= {$minBatch} ORDER BY `batch` DESC, `name` DESC"
        );

        $rows = $result ? $this->extractRows($result) : [];

        if (!$rows) {
            $this->output->comment('Nothing to roll back.');
            return;
        }

        foreach ($rows as $row) {
            $name = (string)$row['name'];
            $file = $this->migrationsPath . DIRECTORY_SEPARATOR . $name . '.php';

            if (!is_file($file)) {
                $this->output->warning("Migration file not found, skipping rollback: {$name}");
                continue;
            }

            $this->output->info("Rolling back: {$name}");
            $object = $this->loadClass($file, 'M');
            $object->down();

            $this->db->query(
                "DELETE FROM `{$this->prefix}" . self::MIGRATION_TABLE
                . "` WHERE `name` = '" . $this->db->escape($name) . "'"
            );
        }

        $this->output->info('Rollback complete.');
    }

    /** Return a list of applied migration names. */
    public function appliedMigrations(): array
    {
        $this->ensureMigrationTable();

        $result = $this->db->query(
            "SELECT `name`, `batch` FROM `{$this->prefix}" . self::MIGRATION_TABLE . "` ORDER BY `batch`, `name`"
        );

        return $result ? $this->extractRows($result) : [];
    }

    // ------------------------------------------------------------------ //
    // Internal helpers                                                    //
    // ------------------------------------------------------------------ //

    private function ensureMigrationTable(): void
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `{$this->prefix}" . self::MIGRATION_TABLE . "` (
              `migration_id` int(11) NOT NULL AUTO_INCREMENT,
              `name`         varchar(255) NOT NULL,
              `batch`        int(11) NOT NULL,
              PRIMARY KEY (`migration_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    /** Files NOT yet recorded in the migration table, sorted by name. */
    private function pendingFiles(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $applied = array_column($this->appliedMigrations(), 'name');
        $files   = $this->globSorted($path);

        return array_filter($files, fn($f) => !in_array(basename($f, '.php'), $applied, true));
    }

    private function globSorted(string $path): array
    {
        $files = glob(rtrim($path, '/\\') . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files);
        return $files;
    }

    private function currentBatch(): int
    {
        $result = $this->db->query(
            "SELECT MAX(`batch`) AS `max_batch` FROM `{$this->prefix}" . self::MIGRATION_TABLE . "`"
        );
        $rows = $result ? $this->extractRows($result) : [];
        return (int)(($rows[0]['max_batch'] ?? 0)) + 1;
    }

    /**
     * Require the file, resolve the class name, instantiate it.
     *
     * @param non-empty-string $prefix  'M' for Migration, 'S' for Seeder
     * @return Migration|Seeder
     */
    private function loadClass(string $file, string $prefix): object
    {
        require_once $file;

        $basename  = basename($file, '.php');
        // Remove underscores and CamelCase: "2024_03_14_0001_initial_schema" -> "202403140001InitialSchema"
        $className = $prefix . str_replace(' ', '', ucwords(str_replace('_', ' ', $basename)));

        if (!class_exists($className)) {
            throw new \RuntimeException("Class {$className} not found in {$file}");
        }

        return new $className($this->db, $this->prefix);
    }

    /** Extract rows array from a DB result (handles both object and array returns). */
    private function extractRows(mixed $result): array
    {
        if (is_object($result) && property_exists($result, 'rows')) {
            return (array)$result->rows;
        }
        if (is_array($result)) {
            return $result;
        }
        return [];
    }
}
