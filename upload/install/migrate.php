<?php
/**
 * OCLite Artisan CLI
 *
 * This file bootstraps the OCLite environment and hands off to
 * oclite/console + oclite/database for command dispatching.
 *
 * Usage:
 *   php migrate.php                        # list available commands
 *   php migrate.php migrate                # run pending migrations
 *   php migrate.php migrate:fresh          # drop all + migrate
 *   php migrate.php migrate:fresh --seed   # drop all + migrate + seed
 *   php migrate.php migrate:rollback       # roll back last batch
 *   php migrate.php migrate:status         # show migration status
 *   php migrate.php db:seed                # run all seeders
 *   php migrate.php make:migration name    # scaffold a migration file
 *   php migrate.php make:seeder name       # scaffold a seeder file
 *   php migrate.php make:factory name      # scaffold a factory file
 *
 * Environment-compatible, Docker-ready:
 *   Reads DB credentials from environment variables when present,
 *   otherwise falls back to Docker Compose defaults.
 */

// ------------------------------------------------------------------ //
// 1. Paths                                                            //
// ------------------------------------------------------------------ //

if (!defined('DIR_OPENCART')) define('DIR_OPENCART', str_replace('\\', '/', realpath(__DIR__ . '/../')) . '/');
if (!defined('DIR_SYSTEM'))   define('DIR_SYSTEM',   DIR_OPENCART . 'system/');
if (!defined('DIR_INSTALL'))  define('DIR_INSTALL',  DIR_OPENCART . 'install/');
if (!defined('DIR_STORAGE'))  define('DIR_STORAGE',  DIR_SYSTEM . 'storage/');

// ------------------------------------------------------------------ //
// 2. Composer autoloader (loads oclite/console + oclite/database)    //
// ------------------------------------------------------------------ //

$vendorAutoload = DIR_STORAGE . 'vendor/autoload.php';

if (!is_file($vendorAutoload)) {
    fwrite(STDERR, "ERROR: vendor/autoload.php not found. Run: composer install\n");
    exit(1);
}

require_once $vendorAutoload;

// ------------------------------------------------------------------ //
// 3. OpenCart core bootstrap                                          //
// ------------------------------------------------------------------ //

require_once DIR_SYSTEM . 'engine/autoloader.php';
require_once DIR_SYSTEM . 'library/db.php';
require_once DIR_SYSTEM . 'library/db/mysqli.php';
require_once DIR_SYSTEM . 'helper/general.php';
require_once DIR_SYSTEM . 'helper/utf8.php';

// ------------------------------------------------------------------ //
// 4. DB config (env vars take precedence over defaults)              //
// ------------------------------------------------------------------ //

$db_config = [
    'driver'   => getenv('DB_DRIVER')   ?: 'mysqli',
    'hostname' => getenv('DB_HOSTNAME') ?: 'mysql',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: 'opencart',
    'database' => getenv('DB_DATABASE') ?: 'opencart',
    'port'     => getenv('DB_PORT')     ?: '3306',
    'prefix'   => getenv('DB_PREFIX')   ?: 'oc_',
];

try {
    $db = new \Opencart\System\Library\DB(
        $db_config['driver'],
        $db_config['hostname'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database'],
        $db_config['port']
    );
} catch (\Exception $e) {
    fwrite(STDERR, "DB Error: " . $e->getMessage() . "\n");
    exit(1);
}

if (!defined('DB_DRIVER'))   define('DB_DRIVER',   $db_config['driver']);
if (!defined('DB_HOSTNAME')) define('DB_HOSTNAME', $db_config['hostname']);
if (!defined('DB_USERNAME')) define('DB_USERNAME', $db_config['username']);
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', $db_config['password']);
if (!defined('DB_DATABASE')) define('DB_DATABASE', $db_config['database']);
if (!defined('DB_PORT'))     define('DB_PORT',     $db_config['port']);
if (!defined('DB_PREFIX'))   define('DB_PREFIX',   $db_config['prefix']);

// ------------------------------------------------------------------ //
// 5. DB adapter: wrap OCLite DB in oclite/database DatabaseInterface //
// ------------------------------------------------------------------ //

/**
 * Thin adapter that makes \Opencart\System\Library\DB compatible with
 * \Oclite\Database\DatabaseInterface without touching the core library.
 */
$dbAdapter = new class ($db) implements \Oclite\Database\DatabaseInterface {
    public function __construct(private readonly \Opencart\System\Library\DB $db) {}

    public function query(string $sql): mixed
    {
        return $this->db->query($sql);
    }

    public function getLastId(): int
    {
        return $this->db->getLastId();
    }

    public function escape(string $value): string
    {
        return $this->db->escape($value);
    }
};

// ------------------------------------------------------------------ //
// 6. Runner + Console Application                                     //
// ------------------------------------------------------------------ //

$migrationsPath = DIR_INSTALL . 'database/migrations';
$seedsPath      = DIR_INSTALL . 'database/seeds';
$factoriesPath  = DIR_INSTALL . 'database/factories';

$output = new \Oclite\Console\Output();

$runner = new \Oclite\Database\Runner(
    $dbAdapter,
    $db_config['prefix'],
    $migrationsPath,
    $seedsPath,
    $output,
    $factoriesPath,
);

$app = new \Oclite\Console\Application('OCLite Artisan', '2.0');

\Oclite\Database\DatabaseServiceProvider::register($app, $runner);

// ------------------------------------------------------------------ //
// 7. Extra project-level commands can be added here:                 //
//                                                                    //
//   $app->add(new \MyProject\InstallFreshCommand());                 //
//   $app->add(new \MyProject\SomeOtherCommand());                    //
// ------------------------------------------------------------------ //

exit($app->run());
