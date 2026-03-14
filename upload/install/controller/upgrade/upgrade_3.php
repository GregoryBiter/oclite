<?php
namespace Opencart\Install\Controller\Upgrade;
/**
 * Class Upgrade3
 *
 * @package Opencart\Install\Controller\Upgrade
 */
class Upgrade3 extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('upgrade/upgrade');

		$json = [];

		// It makes mass changes to the DB by creating tables that are not in the current db, changes the charset and DB engine to the SQL schema.
		try {
			// Use migration system instead of legacy db_schema
			require_once(DIR_INSTALL . 'controller/upgrade/migrate.php');
			$migrate = new \Opencart\Install\Controller\Upgrade\Migrate($this->registry);
			$migrate->migrate();
		} catch (\ErrorException $exception) {
			$json['error'] = sprintf($this->language->get('error_exception'), $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
		}

		if (!$json) {
			$json['text'] = sprintf($this->language->get('text_patch'), 3, 3, 11);

			$url = '';

			if (isset($this->request->get['version'])) {
				$url .= '&version=' . $this->request->get['version'];
			}

			if (isset($this->request->get['admin'])) {
				$url .= '&admin=' . $this->request->get['admin'];
			}

			$json['next'] = $this->url->link('upgrade/upgrade_4', $url, true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
