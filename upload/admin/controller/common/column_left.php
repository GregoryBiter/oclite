<?php
namespace Opencart\Admin\Controller\Common;
/**
 * Class Column Left
 *
 * Can be loaded using $this->load->controller('common/column_left');
 *
 * @package Opencart\Admin\Controller\Common
 */
class ColumnLeft extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return string
	 */
	public function index(): string {
		if (isset($this->request->get['user_token']) && isset($this->session->data['user_token']) && ((string)$this->request->get['user_token'] == $this->session->data['user_token'])) {
			$this->load->language('common/column_left');

			// Create a 3 level menu array
			// Level 2 cannot have children

			// Menu
			$data['menus'][] = [
				'id'       => 'menu-dashboard',
				'icon'     => 'fas fa-home',
				'name'     => $this->language->get('text_dashboard'),
				'href'     => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
				'children' => []
			];

			// Catalog
			$catalog = [];

			if ($this->user->hasPermission('access', 'catalog/download')) {
				$catalog[] = [
					'name'     => $this->language->get('text_download'),
					'href'     => $this->url->link('catalog/download', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'catalog/information')) {
				$catalog[] = [
					'name'     => $this->language->get('text_information'),
					'href'     => $this->url->link('catalog/information', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($catalog) {
				$data['menus'][] = [
					'id'       => 'menu-catalog',
					'icon'     => 'fa-solid fa-tag',
					'name'     => $this->language->get('text_catalog'),
					'href'     => '',
					'children' => $catalog
				];
			}

			$cms = [];

			if ($this->user->hasPermission('access', 'cms/topic')) {
				$cms[] = [
					'name'     => $this->language->get('text_topic'),
					'href'     => $this->url->link('cms/topic', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'cms/article')) {
				$cms[] = [
					'name'     => $this->language->get('text_article'),
					'href'     => $this->url->link('cms/article', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'cms/comment')) {
				$cms[] = [
					'name'     => $this->language->get('text_comment'),
					'href'     => $this->url->link('cms/comment', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'cms/antispam')) {
				$cms[] = [
					'name'     => $this->language->get('text_antispam'),
					'href'     => $this->url->link('cms/antispam', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($cms) {
				$data['menus'][] = [
					'id'       => 'menu-cms',
					'icon'     => 'fa-regular fa-newspaper',
					'name'     => $this->language->get('text_cms'),
					'href'     => '',
					'children' => $cms
				];
			}

			// Extension
			$marketplace = [];

			if ($this->user->hasPermission('access', 'marketplace/installer')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_installer'),
					'href'     => $this->url->link('marketplace/installer', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/extension')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_extension'),
					'href'     => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/modification')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_modification'),
					'href'     => $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/startup')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_startup'),
					'href'     => $this->url->link('marketplace/startup', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/event')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_event'),
					'href'     => $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/cron')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_cron'),
					'href'     => $this->url->link('marketplace/cron', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($marketplace) {
				$data['menus'][] = [
					'id'       => 'menu-extension',
					'icon'     => 'fas fa-puzzle-piece',
					'name'     => $this->language->get('text_extension'),
					'href'     => '',
					'children' => $marketplace
				];
			}

			// Design
			$design = [];

			if ($this->user->hasPermission('access', 'design/layout')) {
				$design[] = [
					'name'     => $this->language->get('text_layout'),
					'href'     => $this->url->link('design/layout', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'design/theme')) {
				$design[] = [
					'name'     => $this->language->get('text_theme'),
					'href'     => $this->url->link('design/theme', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'design/translation')) {
				$design[] = [
					'name'     => $this->language->get('text_language_editor'),
					'href'     => $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'design/seo_url')) {
				$design[] = [
					'name'     => $this->language->get('text_seo_url'),
					'href'     => $this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($design) {
				$data['menus'][] = [
					'id'       => 'menu-design',
					'icon'     => 'fas fa-desktop',
					'name'     => $this->language->get('text_design'),
					'href'     => '',
					'children' => $design
				];
			}
			// Customer
			$customer = [];

			if ($this->user->hasPermission('access', 'customer/customer')) {
				$customer[] = [
					'name'     => $this->language->get('text_customer'),
					'href'     => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'customer/customer_group')) {
				$customer[] = [
					'name'     => $this->language->get('text_customer_group'),
					'href'     => $this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'customer/customer_approval')) {
				$customer[] = [
					'name'     => $this->language->get('text_customer_approval'),
					'href'     => $this->url->link('customer/customer_approval', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'customer/gdpr')) {
				$customer[] = [
					'name'     => $this->language->get('text_gdpr'),
					'href'     => $this->url->link('customer/gdpr', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'customer/custom_field')) {
				$customer[] = [
					'name'     => $this->language->get('text_custom_field'),
					'href'     => $this->url->link('customer/custom_field', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($customer) {
				$data['menus'][] = [
					'id'       => 'menu-customer',
					'icon'     => 'fas fa-user',
					'name'     => $this->language->get('text_customer'),
					'href'     => '',
					'children' => $customer
				];
			}

			// System
			$system = [];

			if ($this->user->hasPermission('access', 'setting/setting')) {
				$system[] = [
					'name'     => $this->language->get('text_setting'),
					'href'     => $this->url->link('setting/store', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			// Users
			$user = [];

			if ($this->user->hasPermission('access', 'user/user')) {
				$user[] = [
					'name'     => $this->language->get('text_users'),
					'href'     => $this->url->link('user/user', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'user/user_permission')) {
				$user[] = [
					'name'     => $this->language->get('text_user_group'),
					'href'     => $this->url->link('user/user_permission', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'user/api')) {
				$user[] = [
					'name'     => $this->language->get('text_api'),
					'href'     => $this->url->link('user/api', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($user) {
				$system[] = [
					'name'     => $this->language->get('text_users'),
					'href'     => '',
					'children' => $user
				];
			}

			// Localisation
			$localisation = [];

			if ($this->user->hasPermission('access', 'localisation/language')) {
				$localisation[] = [
					'name'     => $this->language->get('text_language'),
					'href'     => $this->url->link('localisation/language', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'localisation/stock_status')) {
				$localisation[] = [
					'name'     => $this->language->get('text_stock_status'),
					'href'     => $this->url->link('localisation/stock_status', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($localisation) {
				$system[] = [
					'name'     => $this->language->get('text_localisation'),
					'href'     => '',
					'children' => $localisation
				];
			}

			// Tools
			$maintenance = [];

			if ($this->user->hasPermission('access', 'tool/upgrade')) {
				$maintenance[] = [
					'name'     => $this->language->get('text_upgrade'),
					'href'     => $this->url->link('tool/upgrade', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'tool/backup')) {
				$maintenance[] = [
					'name'     => $this->language->get('text_backup'),
					'href'     => $this->url->link('tool/backup', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'tool/upload')) {
				$maintenance[] = [
					'name'     => $this->language->get('text_upload'),
					'href'     => $this->url->link('tool/upload', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'tool/log')) {
				$maintenance[] = [
					'name'     => $this->language->get('text_log'),
					'href'     => $this->url->link('tool/log', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($maintenance) {
				$system[] = [
					'name'     => $this->language->get('text_maintenance'),
					'href'     => '',
					'children' => $maintenance
				];
			}

			if ($system) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'fas fa-cog',
					'name'     => $this->language->get('text_system'),
					'href'     => '',
					'children' => $system
				];
			}

			return $this->load->view('common/column_left', $data);
		} else {
			return '';
		}
	}
}
