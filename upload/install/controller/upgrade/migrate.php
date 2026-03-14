<?php
namespace Opencart\Install\Controller\Upgrade;

/**
 * Class Migrate
 *
 * @package Opencart\Install\Controller\Upgrade
 */
class Migrate extends \Opencart\System\Engine\Controller {
    /**
     * CLI entry point
     */
    public function index(): void {
        $args = $this->request->get['args'] ?? [];
        $command = array_shift($args) ?? 'help';

        switch ($command) {
            case 'migrate': $this->migrate(); break;
            case 'migrate:fresh': $this->fresh(); break;
            case 'db:seed': $this->seed(); break;
            case 'install:fresh': $this->installFresh(); break;
            default:
                echo "Usage: php migrate.php [command]\n";
                break;
        }
    }

    public function migrate(): void {
        echo "Running migrations...\n";
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "migration` (
            `migration_id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `batch` int(11) NOT NULL,
            PRIMARY KEY (`migration_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $files = glob(DIR_INSTALL . 'database/migrations/*.php');
        sort($files);

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "migration` WHERE `name` = '" . $this->db->escape($name) . "'");
            
            if (!$query->num_rows) {
                echo "Migrating: $name\n";
                require_once($file);
                $class = 'M' . str_replace(['_', '-'], '', $name);
                if (class_exists($class)) {
                    $migration = new $class($this->registry);
                    $migration->up();
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "migration` SET `name` = '" . $this->db->escape($name) . "', `batch` = '1'");
                }
            }
        }
    }

    public function fresh(): void {
        echo "Dropping all tables...\n";
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        $tables = $this->db->query("SHOW TABLES");
        foreach ($tables->rows as $table) {
            $name = array_values($table)[0];
            $this->db->query("DROP TABLE `$name` ");
        }
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
        echo "All tables dropped.\n";
        $this->migrate();
    }

    public function seed(): void {
        echo "Seeding database...\n";
        $files = glob(DIR_INSTALL . 'database/seeds/*.php');
        sort($files);
        foreach ($files as $file) {
            $name = basename($file, '.php');
            echo "Seeding: $name\n";
            require_once($file);
            $class = 'S' . str_replace(['_', '-'], '', $name);
            if (class_exists($class)) {
                $seeder = new $class($this->registry);
                $seeder->run();
            }
        }
    }

    public function installFresh(string $http_server = 'http://localhost/'): void {
        echo "\033[33mStarting FULL OCLite re-installation...\033[0m\n";

        $this->fresh();
        $this->seed();

        echo "Creating admin account (admin/admin)...\n";
        $password = password_hash('admin', PASSWORD_DEFAULT);
        $this->db->query("DELETE FROM `" . DB_PREFIX . "user` WHERE `username` = 'admin'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET `user_group_id` = '1', `username` = 'admin', `password` = '" . $this->db->escape($password) . "', `firstname` = 'Admin', `lastname` = 'OCLite', `email` = 'admin@localhost', `status` = '1', `date_added` = NOW()");

        echo "Generating config files...\n";
        $this->writeConfigFiles($http_server);

        // Создаём .htaccess если его ещё нет
        if (!is_file(DIR_OPENCART . '.htaccess') && is_file(DIR_OPENCART . '.htaccess.txt')) {
            copy(DIR_OPENCART . '.htaccess.txt', DIR_OPENCART . '.htaccess');
        }

        echo "\033[32mOCLite re-installed! Visit " . $http_server . "admin/\033[0m\n";
    }

    private function writeConfigFiles(string $http_server): void {
        // Catalog config.php
        $catalog  = '<?php' . "\n";
        $catalog .= "define('APPLICATION', 'Catalog');\n\n";
        $catalog .= "define('HTTP_SERVER', '" . $http_server . "');\n\n";
        $catalog .= "define('DIR_OPENCART', '" . DIR_OPENCART . "');\n";
        $catalog .= "define('DIR_APPLICATION', DIR_OPENCART . 'catalog/');\n";
        $catalog .= "define('DIR_EXTENSION', DIR_OPENCART . 'extension/');\n";
        $catalog .= "define('DIR_IMAGE', DIR_OPENCART . 'image/');\n";
        $catalog .= "define('DIR_SYSTEM', DIR_OPENCART . 'system/');\n";
        $catalog .= "define('DIR_STORAGE', DIR_SYSTEM . 'storage/');\n";
        $catalog .= "define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');\n";
        $catalog .= "define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');\n";
        $catalog .= "define('DIR_CONFIG', DIR_SYSTEM . 'config/');\n";
        $catalog .= "define('DIR_CACHE', DIR_STORAGE . 'cache/');\n";
        $catalog .= "define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');\n";
        $catalog .= "define('DIR_LOGS', DIR_STORAGE . 'logs/');\n";
        $catalog .= "define('DIR_SESSION', DIR_STORAGE . 'session/');\n";
        $catalog .= "define('DIR_UPLOAD', DIR_STORAGE . 'upload/');\n\n";
        $catalog .= "define('DB_DRIVER', '" . DB_DRIVER . "');\n";
        $catalog .= "define('DB_HOSTNAME', '" . DB_HOSTNAME . "');\n";
        $catalog .= "define('DB_USERNAME', '" . DB_USERNAME . "');\n";
        $catalog .= "define('DB_PASSWORD', '" . DB_PASSWORD . "');\n";
        $catalog .= "define('DB_DATABASE', '" . DB_DATABASE . "');\n";
        $catalog .= "define('DB_PORT', '" . DB_PORT . "');\n";
        $catalog .= "define('DB_PREFIX', '" . DB_PREFIX . "');\n";
        $catalog .= "define('DB_SSL_KEY', '');\n";
        $catalog .= "define('DB_SSL_CERT', '');\n";
        $catalog .= "define('DB_SSL_CA', '');\n";

        file_put_contents(DIR_OPENCART . 'config.php', $catalog);

        // Admin config.php
        $admin  = '<?php' . "\n";
        $admin .= "define('APPLICATION', 'Admin');\n\n";
        $admin .= "define('HTTP_SERVER', '" . $http_server . "admin/');\n";
        $admin .= "define('HTTP_CATALOG', '" . $http_server . "');\n\n";
        $admin .= "define('DIR_OPENCART', '" . DIR_OPENCART . "');\n";
        $admin .= "define('DIR_APPLICATION', DIR_OPENCART . 'admin/');\n";
        $admin .= "define('DIR_EXTENSION', DIR_OPENCART . 'extension/');\n";
        $admin .= "define('DIR_IMAGE', DIR_OPENCART . 'image/');\n";
        $admin .= "define('DIR_SYSTEM', DIR_OPENCART . 'system/');\n";
        $admin .= "define('DIR_CATALOG', DIR_OPENCART . 'catalog/');\n";
        $admin .= "define('DIR_STORAGE', DIR_SYSTEM . 'storage/');\n";
        $admin .= "define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');\n";
        $admin .= "define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');\n";
        $admin .= "define('DIR_CONFIG', DIR_SYSTEM . 'config/');\n";
        $admin .= "define('DIR_CACHE', DIR_STORAGE . 'cache/');\n";
        $admin .= "define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');\n";
        $admin .= "define('DIR_LOGS', DIR_STORAGE . 'logs/');\n";
        $admin .= "define('DIR_SESSION', DIR_STORAGE . 'session/');\n";
        $admin .= "define('DIR_UPLOAD', DIR_STORAGE . 'upload/');\n\n";
        $admin .= "define('DB_DRIVER', '" . DB_DRIVER . "');\n";
        $admin .= "define('DB_HOSTNAME', '" . DB_HOSTNAME . "');\n";
        $admin .= "define('DB_USERNAME', '" . DB_USERNAME . "');\n";
        $admin .= "define('DB_PASSWORD', '" . DB_PASSWORD . "');\n";
        $admin .= "define('DB_DATABASE', '" . DB_DATABASE . "');\n";
        $admin .= "define('DB_PORT', '" . DB_PORT . "');\n";
        $admin .= "define('DB_PREFIX', '" . DB_PREFIX . "');\n\n";
        $admin .= "define('DB_SSL_KEY', '');\n";
        $admin .= "define('DB_SSL_CERT', '');\n";
        $admin .= "define('DB_SSL_CA', '');\n";

        file_put_contents(DIR_OPENCART . 'admin/config.php', $admin);
    }
}
