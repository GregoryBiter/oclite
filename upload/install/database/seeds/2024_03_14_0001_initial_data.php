<?php
use Oclite\Database\Seeder;

/**
 * Minimal CMS seed data — no store/product/customer/order functionality.
 */
class S202403140001InitialData extends Seeder {

    public function run(): void {
        $p = $this->prefix;

        // ------------------------------------------------------------------ //
        // Language                                                            //
        // ------------------------------------------------------------------ //
        $this->db->query("INSERT INTO `{$p}language` (`language_id`, `name`, `code`, `locale`, `extension`, `sort_order`, `status`) VALUES
            (1, 'English', 'en-gb', 'en-gb,en', 'opencart', 1, 1)");

        // ------------------------------------------------------------------ //
        // User group — Administrator with CMS-only permissions               //
        // ------------------------------------------------------------------ //
        $permission = json_encode([
            'access' => [
                'cms/antispam', 'cms/article', 'cms/comment', 'cms/topic',
                'common/developer', 'common/filemanager', 'common/security',
                'design/banner', 'design/layout', 'design/seo_url', 'design/theme', 'design/translation',
                'error/exception',
                'event/modification',
                'extension/captcha', 'extension/dashboard', 'extension/language',
                'extension/marketplace', 'extension/module', 'extension/other', 'extension/theme',
                'catalog/information', 'catalog/identifier',
                'localisation/language',
                'mail/authorize', 'mail/forgotten',
                'marketplace/cron', 'marketplace/event', 'marketplace/extension',
                'marketplace/installer', 'marketplace/marketplace', 'marketplace/modification',
                'marketplace/startup',
                'report/online', 'report/statistics',
                'setting/setting', 'setting/store',
                'tool/backup', 'tool/log', 'tool/notification', 'tool/upgrade', 'tool/upload',
                'user/profile', 'user/user', 'user/user_permission',
                'extension/opencart/captcha/basic',
                'extension/opencart/dashboard/activity',
                'extension/opencart/dashboard/chart',
                'extension/opencart/dashboard/customer',
                'extension/opencart/dashboard/map',
                'extension/opencart/dashboard/online',
                'extension/opencart/dashboard/order',
                'extension/opencart/dashboard/recent',
                'extension/opencart/dashboard/sale',
                'extension/opencart/module/account',
                'extension/opencart/module/banner',
                'extension/opencart/module/blog',
                'extension/opencart/module/html',
                'extension/opencart/module/information',
                'extension/opencart/module/topic',
                'extension/opencart/theme/basic',
            ],
            'modify' => [
                'cms/antispam', 'cms/article', 'cms/comment', 'cms/topic',
                'common/developer', 'common/filemanager', 'common/security',
                'design/banner', 'design/layout', 'design/seo_url', 'design/theme', 'design/translation',
                'error/exception',
                'event/modification',
                'extension/captcha', 'extension/dashboard', 'extension/language',
                'extension/marketplace', 'extension/module', 'extension/other', 'extension/theme',
                'catalog/information', 'catalog/identifier',
                'localisation/language',
                'mail/authorize', 'mail/forgotten',
                'marketplace/cron', 'marketplace/event', 'marketplace/extension',
                'marketplace/installer', 'marketplace/marketplace', 'marketplace/modification',
                'marketplace/startup',
                'report/online', 'report/statistics',
                'setting/setting', 'setting/store',
                'tool/backup', 'tool/log', 'tool/notification', 'tool/upgrade', 'tool/upload',
                'user/profile', 'user/user', 'user/user_permission',
                'extension/opencart/captcha/basic',
                'extension/opencart/dashboard/activity',
                'extension/opencart/dashboard/chart',
                'extension/opencart/dashboard/customer',
                'extension/opencart/dashboard/map',
                'extension/opencart/dashboard/online',
                'extension/opencart/dashboard/order',
                'extension/opencart/dashboard/recent',
                'extension/opencart/dashboard/sale',
                'extension/opencart/module/account',
                'extension/opencart/module/banner',
                'extension/opencart/module/blog',
                'extension/opencart/module/html',
                'extension/opencart/module/information',
                'extension/opencart/module/topic',
                'extension/opencart/theme/basic',
            ],
        ]);
        $this->db->query("INSERT INTO `{$p}user_group` (`user_group_id`, `name`, `permission`) VALUES
            (1, 'Administrator', '" . $this->db->escape($permission) . "')");

        // ------------------------------------------------------------------ //
        // OpenCart default extension package                                  //
        // ------------------------------------------------------------------ //
        $this->db->query("INSERT INTO `{$p}extension_install`
            (`extension_install_id`, `extension_id`, `extension_download_id`, `name`, `description`, `code`, `version`, `author`, `link`, `status`, `date_added`)
            VALUES (1, 0, 0, 'OpenCart Default Extensions',
            'This extension contains all the default extensions for modules, themes and other components.',
            'opencart', '1.0', 'OpenCart Ltd', 'http://www.opencart.com', 1, NOW())");

        // Extension paths (CMS-relevant only)
        $paths = [
            'opencart',
            'opencart/admin',
            'opencart/admin/controller',
            'opencart/admin/controller/captcha',
            'opencart/admin/controller/captcha/basic.php',
            'opencart/admin/controller/dashboard',
            'opencart/admin/controller/dashboard/activity.php',
            'opencart/admin/controller/dashboard/chart.php',
            'opencart/admin/controller/dashboard/customer.php',
            'opencart/admin/controller/dashboard/map.php',
            'opencart/admin/controller/dashboard/online.php',
            'opencart/admin/controller/dashboard/order.php',
            'opencart/admin/controller/dashboard/recent.php',
            'opencart/admin/controller/dashboard/sale.php',
            'opencart/admin/controller/module',
            'opencart/admin/controller/module/account.php',
            'opencart/admin/controller/module/banner.php',
            'opencart/admin/controller/module/blog.php',
            'opencart/admin/controller/module/html.php',
            'opencart/admin/controller/module/information.php',
            'opencart/admin/controller/module/topic.php',
            'opencart/admin/controller/theme',
            'opencart/admin/controller/theme/basic.php',
            'opencart/admin/language',
            'opencart/admin/language/en-gb',
            'opencart/admin/language/en-gb/captcha',
            'opencart/admin/language/en-gb/captcha/basic.php',
            'opencart/admin/language/en-gb/dashboard',
            'opencart/admin/language/en-gb/dashboard/activity.php',
            'opencart/admin/language/en-gb/dashboard/chart.php',
            'opencart/admin/language/en-gb/dashboard/customer.php',
            'opencart/admin/language/en-gb/dashboard/map.php',
            'opencart/admin/language/en-gb/dashboard/online.php',
            'opencart/admin/language/en-gb/dashboard/order.php',
            'opencart/admin/language/en-gb/dashboard/recent.php',
            'opencart/admin/language/en-gb/dashboard/sale.php',
            'opencart/admin/language/en-gb/module',
            'opencart/admin/language/en-gb/module/account.php',
            'opencart/admin/language/en-gb/module/banner.php',
            'opencart/admin/language/en-gb/module/blog.php',
            'opencart/admin/language/en-gb/module/html.php',
            'opencart/admin/language/en-gb/module/information.php',
            'opencart/admin/language/en-gb/module/topic.php',
            'opencart/admin/language/en-gb/theme',
            'opencart/admin/language/en-gb/theme/basic.php',
            'opencart/admin/view',
            'opencart/admin/view/stylesheet',
            'opencart/admin/view/template',
            'opencart/admin/view/template/captcha',
            'opencart/admin/view/template/captcha/basic.twig',
            'opencart/admin/view/template/dashboard',
            'opencart/admin/view/template/dashboard/activity.twig',
            'opencart/admin/view/template/dashboard/chart.twig',
            'opencart/admin/view/template/dashboard/customer.twig',
            'opencart/admin/view/template/dashboard/map.twig',
            'opencart/admin/view/template/dashboard/online.twig',
            'opencart/admin/view/template/dashboard/order.twig',
            'opencart/admin/view/template/dashboard/recent.twig',
            'opencart/admin/view/template/dashboard/sale.twig',
            'opencart/admin/view/template/module',
            'opencart/admin/view/template/module/account.twig',
            'opencart/admin/view/template/module/banner.twig',
            'opencart/admin/view/template/module/blog.twig',
            'opencart/admin/view/template/module/html.twig',
            'opencart/admin/view/template/module/information.twig',
            'opencart/admin/view/template/module/topic.twig',
            'opencart/admin/view/template/theme',
            'opencart/admin/view/template/theme/basic.twig',
            'opencart/catalog',
            'opencart/catalog/controller',
            'opencart/catalog/controller/captcha',
            'opencart/catalog/controller/captcha/basic.php',
            'opencart/catalog/controller/module',
            'opencart/catalog/controller/module/account.php',
            'opencart/catalog/controller/module/banner.php',
            'opencart/catalog/controller/module/blog.php',
            'opencart/catalog/controller/module/html.php',
            'opencart/catalog/controller/module/information.php',
            'opencart/catalog/controller/module/topic.php',
            'opencart/catalog/language',
            'opencart/catalog/language/en-gb',
            'opencart/catalog/language/en-gb/captcha',
            'opencart/catalog/language/en-gb/captcha/basic.php',
            'opencart/catalog/view',
            'opencart/catalog/view/stylesheet',
            'opencart/catalog/view/template',
            'opencart/catalog/view/template/captcha',
            'opencart/catalog/view/template/captcha/basic.twig',
            'opencart/catalog/view/template/module',
            'opencart/catalog/view/template/module/account.twig',
            'opencart/catalog/view/template/module/banner.twig',
            'opencart/catalog/view/template/module/blog.twig',
            'opencart/catalog/view/template/module/html.twig',
            'opencart/catalog/view/template/module/information.twig',
            'opencart/catalog/view/template/module/topic.twig',
            'opencart/system',
            'opencart/system/config',
            'opencart/system/config/basic.php',
        ];
        $path_values = implode(',', array_map(fn($path) => "(1, '" . $this->db->escape($path) . "')", $paths));
        $this->db->query("INSERT INTO `{$p}extension_path` (`extension_install_id`, `path`) VALUES {$path_values}");

        // ------------------------------------------------------------------ //
        // Extensions registry (CMS-relevant only)                            //
        // ------------------------------------------------------------------ //
        $this->db->query("INSERT INTO `{$p}extension` (`extension`, `type`, `code`) VALUES
            ('opencart', 'captcha', 'basic'),
            ('opencart', 'theme', 'basic'),
            ('opencart', 'module', 'account'),
            ('opencart', 'module', 'banner'),
            ('opencart', 'module', 'blog'),
            ('opencart', 'module', 'html'),
            ('opencart', 'module', 'information'),
            ('opencart', 'module', 'topic'),
            ('opencart', 'dashboard', 'activity'),
            ('opencart', 'dashboard', 'chart'),
            ('opencart', 'dashboard', 'customer'),
            ('opencart', 'dashboard', 'map'),
            ('opencart', 'dashboard', 'online'),
            ('opencart', 'dashboard', 'order'),
            ('opencart', 'dashboard', 'recent'),
            ('opencart', 'dashboard', 'sale')");

        // ------------------------------------------------------------------ //
        // Admin-only events (password reset, 2FA authorize)                  //
        // ------------------------------------------------------------------ //
        $this->db->query("INSERT INTO `{$p}event` (`code`, `description`, `trigger`, `action`, `status`, `sort_order`) VALUES
            ('admin_mail_user_forgotten',
             'Sends mail to users who have forgotten their password.',
             'admin/model/user/user.addToken/after', 'mail/forgotten', 1, 1),
            ('admin_mail_user_authorize',
             'Sends mail login code to users email to authorize login from a new device.',
             'admin/controller/common/authorize.send/after', 'mail/authorize', 1, 1),
            ('admin_mail_user_authorize_reset',
             'Sends reset link to user whose account is locked out after 3 wrong authorize code login attempts.',
             'admin/model/user/user.addToken/after', 'mail/authorize.reset', 1, 1)");

        // ------------------------------------------------------------------ //
        // Layouts                                                             //
        // ------------------------------------------------------------------ //
        $this->db->query("INSERT INTO `{$p}layout` (`layout_id`, `name`) VALUES
            (1, 'Home'),
            (2, 'Default'),
            (3, 'Account'),
            (4, 'Information'),
            (5, 'Blog')");

        $this->db->query("INSERT INTO `{$p}layout_module` (`layout_id`, `code`, `position`, `sort_order`) VALUES
            (3, 'opencart.account', 'column_right', 1),
            (5, 'opencart.topic', 'column_left', 1)");

        $this->db->query("INSERT INTO `{$p}layout_route` (`layout_id`, `store_id`, `route`) VALUES
            (1, 0, 'common/home'),
            (2, 0, ''),
            (3, 0, 'account/%'),
            (4, 0, 'information/information'),
            (5, 0, 'cms/blog'),
            (5, 0, 'cms/blog.info')");

        // ------------------------------------------------------------------ //
        // Settings                                                            //
        // ------------------------------------------------------------------ //
        $settings = [
            // Core
            ['config', 'config_name', 'Your Site', 0],
            ['config', 'config_owner', 'Your Name', 0],
            ['config', 'config_address', '', 0],
            ['config', 'config_email', 'admin@example.com', 0],
            ['config', 'config_telephone', '', 0],
            ['config', 'config_fax', '', 0],
            ['config', 'config_image', '', 0],
            ['config', 'config_logo', 'catalog/opencart-logo.png', 0],
            ['config', 'config_icon', 'catalog/opencart.ico', 0],
            ['config', 'config_description', '{"1":{"meta_title":"Your Site","meta_description":"","meta_keyword":""}}', 1],
            // Language / locale
            ['config', 'config_language_catalog', 'en-gb', 0],
            ['config', 'config_language_admin', 'en-gb', 0],
            ['config', 'config_timezone', 'UTC', 0],
            // Theme & layout
            ['config', 'config_theme', 'basic', 0],
            ['config', 'config_layout_id', '2', 0],
            // Session
            ['config', 'config_session_expire', '86400', 0],
            ['config', 'config_session_samesite', 'Strict', 0],
            // Security
            ['config', 'config_login_attempts', '5', 0],
            ['config', 'config_password_length', '6', 0],
            ['config', 'config_2fa_expire', '90', 0],
            ['config', 'config_encryption', '', 0],
            // Captcha
            ['config', 'config_captcha', 'basic', 0],
            ['config', 'config_captcha_page', '["contact"]', 1],
            // SEO
            ['config', 'config_seo_url', '0', 0],
            // Pagination
            ['config', 'config_pagination', '10', 0],
            ['config', 'config_pagination_admin', '10', 0],
            ['config', 'config_autocomplete_limit', '5', 0],
            // Article settings
            ['config', 'config_article_description_length', '600', 0],
            // File upload
            ['config', 'config_file_max_size', '20', 0],
            ['config', 'config_file_ext_allowed', "zip\r\ntxt\r\npng\r\njpeg\r\nwebp\r\njpg\r\ngif\r\nbmp\r\nico\r\npdf", 0],
            ['config', 'config_file_mime_allowed', "text/plain\r\nimage/png\r\nimage/webp\r\nimage/jpeg\r\nimage/gif\r\nimage/bmp\r\napplication/pdf", 0],
            // Image sizes
            ['config', 'config_image_default_width', '300', 0],
            ['config', 'config_image_default_height', '300', 0],
            ['config', 'config_image_article_width', '1140', 0],
            ['config', 'config_image_article_height', '380', 0],
            ['config', 'config_image_topic_width', '1140', 0],
            ['config', 'config_image_topic_height', '380', 0],
            // Maintenance & error
            ['config', 'config_maintenance', '0', 0],
            ['config', 'config_compression', '0', 0],
            ['config', 'config_error_display', '1', 0],
            ['config', 'config_error_log', '1', 0],
            ['config', 'config_error_filename', 'error.log', 0],
            // Mail
            ['config', 'config_mail_engine', '', 0],
            ['config', 'config_mail_parameter', '', 0],
            ['config', 'config_mail_smtp_hostname', '', 0],
            ['config', 'config_mail_smtp_username', '', 0],
            ['config', 'config_mail_smtp_password', '', 0],
            ['config', 'config_mail_smtp_port', '25', 0],
            ['config', 'config_mail_smtp_timeout', '5', 0],
            ['config', 'config_mail_alert_email', '', 0],
            ['config', 'config_mail_alert', '[]', 1],
            // Dashboards
            ['dashboard_activity', 'dashboard_activity_status', '1', 0],
            ['dashboard_activity', 'dashboard_activity_sort_order', '7', 0],
            ['dashboard_activity', 'dashboard_activity_width', '4', 0],
            ['dashboard_sale', 'dashboard_sale_status', '1', 0],
            ['dashboard_sale', 'dashboard_sale_sort_order', '2', 0],
            ['dashboard_sale', 'dashboard_sale_width', '3', 0],
            ['dashboard_chart', 'dashboard_chart_status', '1', 0],
            ['dashboard_chart', 'dashboard_chart_sort_order', '6', 0],
            ['dashboard_chart', 'dashboard_chart_width', '6', 0],
            ['dashboard_customer', 'dashboard_customer_status', '1', 0],
            ['dashboard_customer', 'dashboard_customer_sort_order', '3', 0],
            ['dashboard_customer', 'dashboard_customer_width', '3', 0],
            ['dashboard_map', 'dashboard_map_status', '1', 0],
            ['dashboard_map', 'dashboard_map_sort_order', '5', 0],
            ['dashboard_map', 'dashboard_map_width', '6', 0],
            ['dashboard_online', 'dashboard_online_status', '1', 0],
            ['dashboard_online', 'dashboard_online_sort_order', '4', 0],
            ['dashboard_online', 'dashboard_online_width', '3', 0],
            ['dashboard_order', 'dashboard_order_status', '1', 0],
            ['dashboard_order', 'dashboard_order_sort_order', '1', 0],
            ['dashboard_order', 'dashboard_order_width', '3', 0],
            ['dashboard_recent', 'dashboard_recent_status', '1', 0],
            ['dashboard_recent', 'dashboard_recent_sort_order', '8', 0],
            ['dashboard_recent', 'dashboard_recent_width', '8', 0],
            // Developer
            ['developer', 'developer_sass', '1', 0],
            // Modules
            ['module_account', 'module_account_status', '1', 0],
            ['module_topic', 'module_topic_status', '1', 0],
            // Theme
            ['theme_basic', 'theme_basic_status', '1', 0],
        ];

        foreach ($settings as [$code, $key, $value, $serialized]) {
            $this->db->query("INSERT INTO `{$p}setting` (`store_id`, `code`, `key`, `value`, `serialized`)
                VALUES (0, '" . $this->db->escape($code) . "', '" . $this->db->escape($key) . "', '" . $this->db->escape($value) . "', " . (int)$serialized . ")");
        }

        // ------------------------------------------------------------------ //
        // Sample CMS pages                                                    //
        // ------------------------------------------------------------------ //
        $this->db->query("INSERT INTO `{$p}information` (`information_id`, `sort_order`, `status`) VALUES
            (1, 1, 1),
            (2, 2, 1)");

        $this->db->query("INSERT INTO `{$p}information_description`
            (`information_id`, `language_id`, `title`, `description`, `meta_title`, `meta_description`, `meta_keyword`) VALUES
            (1, 1, 'About Us', '<p>About Us</p>', 'About Us', '', ''),
            (2, 1, 'Privacy Policy', '<p>Privacy Policy</p>', 'Privacy Policy', '', '')");

        $this->db->query("INSERT INTO `{$p}information_to_store` (`information_id`, `store_id`) VALUES
            (1, 0),
            (2, 0)");

        // ------------------------------------------------------------------ //
        // SEO URLs for CMS routes                                             //
        // ------------------------------------------------------------------ //
        $this->db->query("INSERT INTO `{$p}seo_url` (`store_id`, `language_id`, `key`, `value`, `keyword`, `sort_order`) VALUES
            (0, 1, 'language', 'en-gb', 'en-gb', -2),
            (0, 1, 'route', 'information/information', 'information', -1),
            (0, 1, 'route', 'information/information.info', 'info', 0),
            (0, 1, 'information_id', '1', 'about-us', 0),
            (0, 1, 'information_id', '2', 'privacy', 0)");
    }
}
