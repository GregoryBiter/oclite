<?php
/**
 * OCLite Artisan CLI - Simple Dev Tool
 */

if (!defined('DIR_OPENCART')) define('DIR_OPENCART', str_replace('\\', '/', realpath(__DIR__ . '/../')) . '/');
if (!defined('DIR_SYSTEM')) define('DIR_SYSTEM', DIR_OPENCART . 'system/');
if (!defined('DIR_INSTALL')) define('DIR_INSTALL', DIR_OPENCART . 'install/');
if (!defined('DIR_STORAGE')) define('DIR_STORAGE', DIR_SYSTEM . 'storage/');

// Step 1: Boot DB Config from Docker
$db_config = [
    'driver'   => 'mysqli',
    'hostname' => 'mysql',
    'username' => 'root',
    'password' => 'opencart',
    'database' => 'opencart',
    'port'     => '3306',
    'prefix'   => 'oc_',
];

// Step 2: OpenCart Autoloader & Core
require_once(DIR_SYSTEM . 'engine/autoloader.php');
$autoloader = new \Opencart\System\Engine\Autoloader();
$autoloader->register('Opencart\System', DIR_SYSTEM);
$autoloader->register('Opencart\Install', DIR_INSTALL);

// Manually require library files before using them
require_once(DIR_SYSTEM . 'library/db.php');
require_once(DIR_SYSTEM . 'library/db/mysqli.php');
require_once(DIR_SYSTEM . 'engine/registry.php');
require_once(DIR_SYSTEM . 'engine/loader.php');
require_once(DIR_SYSTEM . 'engine/controller.php');
require_once(DIR_SYSTEM . 'engine/model.php');
require_once(DIR_SYSTEM . 'helper/general.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');

// Step 3: Connection
try {
    $db = new \Opencart\System\Library\DB($db_config['driver'], $db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database'], $db_config['port']);
    if (!defined('DB_DRIVER'))   define('DB_DRIVER',   $db_config['driver']);
    if (!defined('DB_HOSTNAME')) define('DB_HOSTNAME', $db_config['hostname']);
    if (!defined('DB_USERNAME')) define('DB_USERNAME', $db_config['username']);
    if (!defined('DB_PASSWORD')) define('DB_PASSWORD', $db_config['password']);
    if (!defined('DB_DATABASE')) define('DB_DATABASE', $db_config['database']);
    if (!defined('DB_PORT'))     define('DB_PORT',     $db_config['port']);
    if (!defined('DB_PREFIX'))   define('DB_PREFIX',   $db_config['prefix']);
} catch (\Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 4: Registry
$registry = new \Opencart\System\Engine\Registry();
$registry->set('autoloader', $autoloader);
$registry->set('db', $db);
$registry->set('load', new \Opencart\System\Engine\Loader($registry));
$registry->set('request', new class { public $get = []; public $post = []; public $cookie = []; public $files = []; });
$registry->set('response', new class { public function addHeader($h) {} public function setOutput($o) { echo $o; } });

echo "\n\033[32mOCLite Dev Tool\033[0m\n";

$args = array_slice($argv, 1);
$command = $args[0] ?? 'help';

// Parse named options like --http-server=http://localhost/
$options = [];
foreach ($args as $arg) {
    if (preg_match('/^--([a-z\-]+)=(.+)$/', $arg, $m)) {
        $options[$m[1]] = $m[2];
    }
}
$http_server = rtrim($options['http-server'] ?? 'http://localhost/', '/') . '/';

$migrate_controller = new \Opencart\Install\Controller\Upgrade\Migrate($registry);

switch ($command) {
    case 'install:fresh': $migrate_controller->installFresh($http_server); break;
    case 'migrate:fresh': $migrate_controller->fresh(); break;
    case 'db:seed': $migrate_controller->seed(); break;
    case 'migrate': $migrate_controller->migrate(); break;
    default:
        echo "Usage: php migrate.php install:fresh [--http-server=http://localhost/]\n";
        break;
}
