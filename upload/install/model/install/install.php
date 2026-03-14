<?php
namespace Opencart\Install\Model\Install;
/**
 * Class Install
 *
 * @example $install_model = $this->model_install_install;
 *
 * Can be called from $this->load->model('install/install');
 *
 * @package Opencart\Install\Model\Install
 */
class Install extends \Opencart\System\Engine\Model {
	/**
	 * Database
	 *
	 * @param array<string, mixed> $data
	 *
	 * @throws \Exception
	 *
	 * @return void
	 */
public function database(array $data): void {
if (!defined('DIR_OPENCART')) {
define('DIR_OPENCART', str_replace('\', '/', realpath(DIR_APPLICATION . '../')) . '/');
}

$db = new \Opencart\System\Library\DB($data['db_driver'], html_entity_decode($data['db_hostname'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data['db_username'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data['db_password'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data['db_database'], ENT_QUOTES, 'UTF-8'), $data['db_port'], $this->request->post['db_ssl_key'], $this->request->post['db_ssl_cert'], $this->request->post['db_ssl_ca']);

// Регистрация DB в реестре для миграций
$this->registry->set('db', $db);

// Используем миграции для создания таблиц
$migrate = new \Opencart\Install\Controller\Upgrade\Migrate($this->registry);
$migrate->migrate();

// Используем сиды для наполнения данными
$migrate->seed();

// Дополнительная настройка после сидов
$db->query("SET CHARACTER SET utf8mb4");
$db->query("SET @@session.sql_mode = ''");

// Админ-пользователь
$db->query("DELETE FROM `" . $data['db_prefix'] . "user` WHERE `user_id` = '1'");
$db->query("INSERT INTO `" . $data['db_prefix'] . "user` SET `user_id` = '1', `user_group_id` = '1', `username` = '" . $db->escape($data['username']) . "', `password` = '" . $db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', `firstname` = 'John', `lastname` = 'Doe', `email` = '" . $db->escape($data['email']) . "', `status` = '1', `date_added` = NOW()");

// Обновление базовых настроек
$db->query("UPDATE `" . $data['db_prefix'] . "setting` SET `value` = '" . $db->escape($data['email']) . "' WHERE `key` = 'config_email'");
}
}
