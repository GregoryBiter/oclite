<?php
//
// Command line tool for installing cloud version of opencart
//
// Usage:
//
//   Cloud Install
//
//   php cli_cloud.php install --username admin
//                             --email    email@example.com
//                             --password password
//

namespace Install;

ini_set('display_errors', 1);

error_reporting(E_ALL);

// APPLICATION
define('APPLICATION', 'Install');

// DIR
define('DIR_OPENCART', str_replace('\\', '/', realpath(__DIR__ . '/../')) . '/');
define('DIR_SYSTEM', DIR_OPENCART . 'system/');
define('DIR_INSTALL', DIR_OPENCART . 'install/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Engine
require_once(DIR_SYSTEM . 'engine/autoloader.php');
require_once(DIR_SYSTEM . 'engine/controller.php');
require_once(DIR_SYSTEM . 'engine/model.php');
require_once(DIR_SYSTEM . 'engine/registry.php');
require_once(DIR_SYSTEM . 'engine/loader.php');

// Library
require_once(DIR_SYSTEM . 'library/request.php');
require_once(DIR_SYSTEM . 'library/response.php');
require_once(DIR_SYSTEM . 'library/db.php');
require_once(DIR_SYSTEM . 'library/db/mysqli.php');

// Helpers
require_once(DIR_SYSTEM . 'helper/general.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');

// Registry
$registry = new \Opencart\System\Engine\Registry();

// Autoloader
$autoloader = new \Opencart\System\Engine\Autoloader();
$autoloader->register('Opencart\System', DIR_SYSTEM);
$autoloader->register('Opencart\Install', DIR_INSTALL);
$registry->set('autoloader', $autoloader);

// Request
$registry->set('request', new \Opencart\System\Library\Request());

// Response
$response = new \Opencart\System\Library\Response();
$response->addHeader('Content-Type: text/plain; charset=utf-8');
$registry->set('response', $response);

set_error_handler(/**
 * @param int    $code
 * @param string $message
 * @param string $file
 * @param int    $line
 *
 * @throws \ErrorException
 *
 * @return false
 */ function(int $code, string $message, string $file, int $line): bool {
	// error was suppressed with the @-operator
	if (error_reporting() === 0) {
		return false;
	}

	throw new \ErrorException($message, 0, $code, $file, $line);
});

/**
 * CliCloud
 */
class CliCloud extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		if (isset($this->request->server['argv'])) {
			$argv = $this->request->server['argv'];
		} else {
			$argv = [];
		}

		// Just displays the path to the file
		$script = array_shift($argv);

		// Get the arguments passed with the command
		$command = array_shift($argv);

		switch ($command) {
			case 'install':
				$output = $this->install($argv);
				break;
			case 'usage':
			default:
				$output = $this->usage();
				break;
		}

		$this->response->setOutput($output);
	}

	/**
	 * @param array<int, string> $argv
	 *
	 * @return string
	 */
	public function install(array $argv): string {
		// Options
		$option = [];

		// Turn args into an array
		for ($i = 0; $i < count($argv); $i++) {
			if (substr($argv[$i], 0, 2) == '--') {
				$key = substr($argv[$i], 2);

				// If the next line also starts with -- we need to fill in a null value for the current one
				if (isset($argv[$i + 1]) && substr($argv[$i + 1], 0, 2) != '--') {
					$option[$key] = $argv[$i + 1];

					// Skip the counter by 2
					$i++;
				} else {
					$option[$key] = '';
				}
			}
		}

		// Required
		$required = [
			'username',
			'email',
			'password'
		];

		// Validation
		$missing = [];

		foreach ($required as $value) {
			if (!array_key_exists($value, $option)) {
				$missing[] = $value;
			}
		}

		if (count($missing)) {
			return 'ERROR: Following inputs were missing or invalid: ' . implode(', ', $missing) . "\n";
		}

		// Pre-installation check
		$error = '';

		if (!oc_validate_length($option['username'], 3, 20)) {
			$error .= 'ERROR: Username must be between 3 and 20 characters!' . "\n";
		}

		if ((oc_strlen($option['email']) > 96) || !filter_var($option['email'], FILTER_VALIDATE_EMAIL)) {
			$error .= 'ERROR: E-Mail Address does not appear to be valid!' . "\n";
		}

		if (!$option['password']) {
			$error .= 'ERROR: Password hash required!' . "\n";
		}

		if ($error) {
			$output  = 'ERROR: Validation failed: ' . "\n";
			$output .= $error . "\n\n";

			return $output;
		}

		$db_driver   = getenv('DB_DRIVER', true);
		$db_hostname = getenv('DB_HOSTNAME', true);
		$db_username = getenv('DB_USERNAME', true);
		$db_password = getenv('DB_PASSWORD', true);
		$db_database = getenv('DB_DATABASE', true);
		$db_port     = getenv('DB_PORT', true);
		$db_prefix   = getenv('DB_PREFIX', true);

		$db_ssl_key  = getenv('DB_SSL_KEY', true);
		$db_ssl_cert = getenv('DB_SSL_CERT', true);
		$db_ssl_ca   = getenv('DB_SSL_CA', true);

		try {
			// Database
			$db = new \Opencart\System\Library\DB($db_driver, $db_hostname, $db_username, $db_password, $db_database, $db_port, $db_ssl_key, $db_ssl_cert, $db_ssl_ca);
		} catch (\Exception $e) {
			return 'ERROR: Could not make a database link using ' . $db_username . '@' . $db_hostname . '!' . "\n";
		}

		// Register DB constants for migrations / seeders
		if (!defined('DB_DRIVER'))   define('DB_DRIVER',   $db_driver);
		if (!defined('DB_HOSTNAME')) define('DB_HOSTNAME', $db_hostname);
		if (!defined('DB_USERNAME')) define('DB_USERNAME', $db_username);
		if (!defined('DB_PASSWORD')) define('DB_PASSWORD', $db_password);
		if (!defined('DB_DATABASE')) define('DB_DATABASE', $db_database);
		if (!defined('DB_PORT'))     define('DB_PORT',     $db_port);
		if (!defined('DB_PREFIX'))   define('DB_PREFIX',   $db_prefix);

		$this->registry->set('db', $db);
		$this->registry->set('load', new \Opencart\System\Engine\Loader($this->registry));

		// Run migrations (drop all tables, recreate from scratch)
		require_once(DIR_INSTALL . 'controller/upgrade/migrate.php');
		$migrate = new \Opencart\Install\Controller\Upgrade\Migrate($this->registry);
		$migrate->fresh();

		// Run seeders
		$migrate->seed();

		// Create admin user (password is passed pre-hashed in cloud mode)
		$db->query("DELETE FROM `" . $db_prefix . "user` WHERE `user_id` = '1'");
		$db->query("INSERT INTO `" . $db_prefix . "user` SET
			`user_id`       = '1',
			`user_group_id` = '1',
			`username`      = '" . $db->escape($option['username']) . "',
			`password`      = '" . $db->escape($option['password']) . "',
			`firstname`     = 'John',
			`lastname`      = 'Doe',
			`email`         = '" . $db->escape($option['email']) . "',
			`status`        = '1',
			`date_added`    = NOW()");

		// Update email in settings
		$db->query("DELETE FROM `" . $db_prefix . "setting` WHERE `key` = 'config_email'");
		$db->query("INSERT INTO `" . $db_prefix . "setting` SET `code` = 'config', `key` = 'config_email', `value` = '" . $db->escape($option['email']) . "'");

		// Generate encryption key
		$db->query("DELETE FROM `" . $db_prefix . "setting` WHERE `key` = 'config_encryption'");
		$db->query("INSERT INTO `" . $db_prefix . "setting` SET `code` = 'config', `key` = 'config_encryption', `value` = '" . $db->escape(oc_token(1024)) . "'");

		// Default API key
		$db->query("INSERT INTO `" . $db_prefix . "api` SET `username` = 'Default', `key` = '" . $db->escape(oc_token(256)) . "', `status` = 1, `date_added` = NOW(), `date_modified` = NOW()");

		$last_id = $db->getLastId();

		$db->query("DELETE FROM `" . $db_prefix . "setting` WHERE `key` = 'config_api_id'");
		$db->query("INSERT INTO `" . $db_prefix . "setting` SET `code` = 'config', `key` = 'config_api_id', `value` = '" . (int)$last_id . "'");

		// Return success message
		return 'SUCCESS! OpenCart successfully installed on your server' . "\n";
	}

	/**
	 * Usage
	 *
	 * @return string
	 */
	public function usage(): string {
		$option = implode(' ', [
			'--username',
			'admin',
			'--email',
			'email@example.com',
			'--password',
			'password'
		]);

		$output  = 'Usage:' . "\n";
		$output .= '======' . "\n\n";
		$output .= 'php cli_install.php install ' . $option . "\n\n";

		return $output;
	}
}

// Controller
$controller = new \Install\CliCloud($registry);
$controller->index();

// Output
$response->output();
