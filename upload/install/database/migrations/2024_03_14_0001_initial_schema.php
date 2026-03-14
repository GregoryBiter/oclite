<?php
use Oclite\Database\Migration;

/**
 * CMS-only schema migration.
 * Creates only the tables required for the lean CMS — no store, product, order,
 * customer, payment, shipping or tax tables.
 */
class M202403140001InitialSchema extends Migration {

    private string $p;

    public function up(): void {
        $this->p = $this->prefix;
        $p = $this->p;

        // ------------------------------------------------------------------ //
        // API                                                                 //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}api` (
          `api_id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(64) NOT NULL DEFAULT '',
          `key` text NOT NULL,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          `date_modified` datetime NOT NULL,
          PRIMARY KEY (`api_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}api_ip` (
          `api_ip_id` int(11) NOT NULL AUTO_INCREMENT,
          `api_id` int(11) NOT NULL,
          `ip` varchar(40) NOT NULL DEFAULT '',
          PRIMARY KEY (`api_ip_id`),
          KEY `api_id` (`api_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}api_history` (
          `api_history_id` int(11) NOT NULL AUTO_INCREMENT,
          `api_id` int(11) NOT NULL,
          `call` varchar(32) NOT NULL DEFAULT '',
          `ip` varchar(40) NOT NULL DEFAULT '',
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`api_history_id`),
          KEY `api_id` (`api_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // Base: store & language have no foreign-key dependencies             //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}store` (
          `store_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(64) NOT NULL DEFAULT '',
          `url` varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`store_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}language` (
          `language_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(32) NOT NULL DEFAULT '',
          `code` varchar(5) NOT NULL DEFAULT '',
          `locale` varchar(255) NOT NULL DEFAULT '',
          `extension` varchar(255) NOT NULL DEFAULT '',
          `sort_order` int(3) NOT NULL DEFAULT 0,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`language_id`),
          KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // Session                                                             //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}session` (
          `session_id` varchar(32) NOT NULL DEFAULT '',
          `data` text NOT NULL,
          `expire` datetime NOT NULL,
          PRIMARY KEY (`session_id`),
          KEY `expire` (`expire`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // Users                                                               //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}user_group` (
          `user_group_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(64) NOT NULL DEFAULT '',
          `permission` text NOT NULL,
          PRIMARY KEY (`user_group_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}user` (
          `user_id` int(11) NOT NULL AUTO_INCREMENT,
          `user_group_id` int(11) NOT NULL DEFAULT 0,
          `username` varchar(20) NOT NULL DEFAULT '',
          `password` varchar(255) NOT NULL DEFAULT '',
          `firstname` varchar(32) NOT NULL DEFAULT '',
          `lastname` varchar(32) NOT NULL DEFAULT '',
          `email` varchar(96) NOT NULL DEFAULT '',
          `image` varchar(255) NOT NULL DEFAULT '',
          `ip` varchar(40) NOT NULL DEFAULT '',
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}user_authorize` (
          `user_authorize_id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `token` varchar(96) NOT NULL DEFAULT '',
          `total` int(1) NOT NULL DEFAULT 0,
          `ip` varchar(40) NOT NULL DEFAULT '',
          `user_agent` varchar(255) NOT NULL DEFAULT '',
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          `date_expire` datetime NOT NULL,
          PRIMARY KEY (`user_authorize_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}user_login` (
          `user_login_id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `ip` varchar(40) NOT NULL DEFAULT '',
          `user_agent` varchar(255) NOT NULL DEFAULT '',
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`user_login_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}user_token` (
          `user_token_id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `code` text NOT NULL,
          `type` varchar(10) NOT NULL DEFAULT '',
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`user_token_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // Extensions                                                          //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}extension` (
          `extension_id` int(11) NOT NULL AUTO_INCREMENT,
          `extension` varchar(255) NOT NULL DEFAULT '',
          `type` varchar(32) NOT NULL DEFAULT '',
          `code` varchar(128) NOT NULL DEFAULT '',
          PRIMARY KEY (`extension_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}extension_install` (
          `extension_install_id` int(11) NOT NULL AUTO_INCREMENT,
          `extension_id` int(11) NOT NULL DEFAULT 0,
          `extension_download_id` int(11) NOT NULL DEFAULT 0,
          `name` varchar(128) NOT NULL DEFAULT '',
          `description` text NOT NULL,
          `code` varchar(255) NOT NULL DEFAULT '',
          `version` varchar(255) NOT NULL DEFAULT '',
          `author` varchar(255) NOT NULL DEFAULT '',
          `link` varchar(255) NOT NULL DEFAULT '',
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`extension_install_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}extension_path` (
          `extension_path_id` int(11) NOT NULL AUTO_INCREMENT,
          `extension_install_id` int(11) NOT NULL,
          `path` varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`extension_path_id`),
          KEY `path` (`path`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}modification` (
          `modification_id` int(11) NOT NULL AUTO_INCREMENT,
          `extension_install_id` int(11) NOT NULL,
          `name` varchar(64) NOT NULL DEFAULT '',
          `description` text NOT NULL,
          `code` varchar(64) NOT NULL DEFAULT '',
          `author` varchar(64) NOT NULL DEFAULT '',
          `version` varchar(32) NOT NULL DEFAULT '',
          `link` varchar(255) NOT NULL DEFAULT '',
          `xml` mediumtext NOT NULL,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`modification_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // System hooks & jobs                                                 //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}event` (
          `event_id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(128) NOT NULL DEFAULT '',
          `description` text NOT NULL,
          `trigger` text NOT NULL,
          `action` text NOT NULL,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `sort_order` int(3) NOT NULL DEFAULT 1,
          PRIMARY KEY (`event_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}cron` (
          `cron_id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(128) NOT NULL DEFAULT '',
          `description` text NOT NULL,
          `cycle` varchar(12) NOT NULL DEFAULT '',
          `action` text NOT NULL,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          `date_modified` datetime NOT NULL,
          PRIMARY KEY (`cron_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}startup` (
          `startup_id` int(11) NOT NULL AUTO_INCREMENT,
          `description` text NOT NULL,
          `code` varchar(64) NOT NULL DEFAULT '',
          `action` text NOT NULL,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `sort_order` int(3) NOT NULL DEFAULT 0,
          PRIMARY KEY (`startup_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // UI / content infrastructure                                         //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}module` (
          `module_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(64) NOT NULL DEFAULT '',
          `code` varchar(64) NOT NULL DEFAULT '',
          `setting` text NOT NULL,
          PRIMARY KEY (`module_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}notification` (
          `notification_id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(64) NOT NULL DEFAULT '',
          `text` text NOT NULL,
          `status` tinyint(11) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`notification_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}statistics` (
          `statistics_id` int(11) NOT NULL AUTO_INCREMENT,
          `code` varchar(64) NOT NULL DEFAULT '',
          `value` decimal(15,4) NOT NULL DEFAULT 0,
          PRIMARY KEY (`statistics_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}upload` (
          `upload_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL DEFAULT '',
          `filename` varchar(255) NOT NULL DEFAULT '',
          `code` varchar(255) NOT NULL DEFAULT '',
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`upload_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // Downloads                                                           //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}download` (
          `download_id` int(11) NOT NULL AUTO_INCREMENT,
          `filename` varchar(255) NOT NULL DEFAULT '',
          `mask` varchar(128) NOT NULL DEFAULT '',
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`download_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}download_description` (
          `download_id` int(11) NOT NULL,
          `language_id` int(11) NOT NULL,
          `name` varchar(64) NOT NULL DEFAULT '',
          PRIMARY KEY (`download_id`, `language_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}download_report` (
          `download_report_id` int(11) NOT NULL AUTO_INCREMENT,
          `download_id` int(11) NOT NULL,
          `ip` varchar(40) NOT NULL DEFAULT '',
          `store_id` int(11) NOT NULL DEFAULT 0,
          `country` varchar(128) NOT NULL DEFAULT '',
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`download_report_id`),
          KEY `download_id` (`download_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}identifier` (
          `identifier_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(64) NOT NULL DEFAULT '',
          `code` varchar(48) NOT NULL DEFAULT '',
          `validation` varchar(255) NOT NULL DEFAULT '',
          `status` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`identifier_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}antispam` (
          `antispam_id` int(11) NOT NULL AUTO_INCREMENT,
          `keyword` varchar(64) NOT NULL DEFAULT '',
          PRIMARY KEY (`antispam_id`),
          KEY `keyword` (`keyword`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // Layout / design                                                     //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}layout` (
          `layout_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(64) NOT NULL DEFAULT '',
          PRIMARY KEY (`layout_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}layout_module` (
          `layout_module_id` int(11) NOT NULL AUTO_INCREMENT,
          `layout_id` int(11) NOT NULL DEFAULT 0,
          `code` varchar(64) NOT NULL DEFAULT '',
          `position` varchar(14) NOT NULL DEFAULT '',
          `sort_order` int(3) NOT NULL DEFAULT 0,
          PRIMARY KEY (`layout_module_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}layout_route` (
          `layout_route_id` int(11) NOT NULL AUTO_INCREMENT,
          `layout_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `route` varchar(64) NOT NULL DEFAULT '',
          PRIMARY KEY (`layout_route_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}setting` (
          `setting_id` int(11) NOT NULL AUTO_INCREMENT,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `code` varchar(128) NOT NULL DEFAULT '',
          `key` varchar(128) NOT NULL DEFAULT '',
          `value` text NOT NULL,
          `serialized` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`setting_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}theme` (
          `theme_id` int(11) NOT NULL AUTO_INCREMENT,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `route` varchar(64) NOT NULL DEFAULT '',
          `code` mediumtext NOT NULL,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`theme_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}translation` (
          `translation_id` int(11) NOT NULL AUTO_INCREMENT,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `language_id` int(11) NOT NULL,
          `route` varchar(64) NOT NULL DEFAULT '',
          `key` varchar(64) NOT NULL DEFAULT '',
          `value` text NOT NULL,
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`translation_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}seo_url` (
          `seo_url_id` int(11) NOT NULL AUTO_INCREMENT,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `language_id` int(11) NOT NULL,
          `key` varchar(64) NOT NULL DEFAULT '',
          `value` varchar(255) NOT NULL DEFAULT '',
          `keyword` varchar(768) NOT NULL DEFAULT '',
          `sort_order` int(3) NOT NULL DEFAULT 0,
          PRIMARY KEY (`seo_url_id`),
          KEY `store` (`store_id`),
          KEY `language` (`language_id`),
          KEY `keyword` (`keyword`),
          KEY `query` (`key`, `value`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // Banners                                                             //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}banner` (
          `banner_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(64) NOT NULL DEFAULT '',
          `status` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`banner_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}banner_image` (
          `banner_image_id` int(11) NOT NULL AUTO_INCREMENT,
          `banner_id` int(11) NOT NULL,
          `language_id` int(11) NOT NULL,
          `title` varchar(64) NOT NULL DEFAULT '',
          `link` varchar(255) NOT NULL DEFAULT '',
          `image` varchar(255) NOT NULL DEFAULT '',
          `sort_order` int(3) NOT NULL DEFAULT 0,
          PRIMARY KEY (`banner_image_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // CMS pages (information)                                             //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}information` (
          `information_id` int(11) NOT NULL AUTO_INCREMENT,
          `sort_order` int(3) NOT NULL DEFAULT 0,
          `status` tinyint(1) NOT NULL DEFAULT 1,
          PRIMARY KEY (`information_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}information_description` (
          `information_id` int(11) NOT NULL,
          `language_id` int(11) NOT NULL,
          `title` varchar(64) NOT NULL DEFAULT '',
          `description` mediumtext NOT NULL,
          `meta_title` varchar(255) NOT NULL DEFAULT '',
          `meta_description` varchar(255) NOT NULL DEFAULT '',
          `meta_keyword` varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`information_id`, `language_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}information_to_layout` (
          `information_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `layout_id` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`information_id`, `store_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}information_to_store` (
          `information_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`information_id`, `store_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // ------------------------------------------------------------------ //
        // Blog: topics (categories) and articles                             //
        // ------------------------------------------------------------------ //

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}topic` (
          `topic_id` int(11) NOT NULL AUTO_INCREMENT,
          `sort_order` int(3) NOT NULL DEFAULT 0,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`topic_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}topic_description` (
          `topic_id` int(11) NOT NULL,
          `language_id` int(11) NOT NULL,
          `name` varchar(255) NOT NULL DEFAULT '',
          `description` text NOT NULL,
          `image` varchar(255) NOT NULL DEFAULT '',
          `meta_title` varchar(255) NOT NULL DEFAULT '',
          `meta_description` varchar(255) NOT NULL DEFAULT '',
          `meta_keyword` varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`topic_id`, `language_id`),
          KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}topic_to_layout` (
          `topic_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `layout_id` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`topic_id`, `store_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}topic_to_store` (
          `topic_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`topic_id`, `store_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}article` (
          `article_id` int(11) NOT NULL AUTO_INCREMENT,
          `topic_id` int(11) NOT NULL DEFAULT 0,
          `author` varchar(64) NOT NULL DEFAULT '',
          `rating` int(11) NOT NULL DEFAULT 0,
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          `date_modified` datetime NOT NULL,
          PRIMARY KEY (`article_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}article_description` (
          `article_id` int(11) NOT NULL,
          `language_id` int(11) NOT NULL,
          `name` varchar(255) NOT NULL DEFAULT '',
          `description` text NOT NULL,
          `image` varchar(255) NOT NULL DEFAULT '',
          `tag` text NOT NULL,
          `meta_title` varchar(255) NOT NULL DEFAULT '',
          `meta_description` varchar(255) NOT NULL DEFAULT '',
          `meta_keyword` varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`article_id`, `language_id`),
          KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}article_comment` (
          `article_comment_id` int(11) NOT NULL AUTO_INCREMENT,
          `article_id` int(11) NOT NULL,
          `parent_id` int(11) NOT NULL DEFAULT 0,
          `customer_id` int(11) NOT NULL DEFAULT 0,
          `author` varchar(64) NOT NULL DEFAULT '',
          `comment` text NOT NULL,
          `rating` int(11) NOT NULL DEFAULT 0,
          `ip` varchar(40) NOT NULL DEFAULT '',
          `status` tinyint(1) NOT NULL DEFAULT 0,
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`article_comment_id`),
          KEY `article_id` (`article_id`),
          KEY `customer_id` (`customer_id`),
          KEY `parent_id` (`parent_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}article_rating` (
          `article_rating_id` int(11) NOT NULL AUTO_INCREMENT,
          `article_comment_id` int(11) NOT NULL,
          `article_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `customer_id` int(11) NOT NULL DEFAULT 0,
          `rating` tinyint(1) NOT NULL DEFAULT 0,
          `ip` varchar(40) NOT NULL DEFAULT '',
          `date_added` datetime NOT NULL,
          PRIMARY KEY (`article_rating_id`),
          KEY `article_comment_id` (`article_comment_id`),
          KEY `article_id` (`article_id`),
          KEY `store_id` (`store_id`),
          KEY `customer_id` (`customer_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}article_to_layout` (
          `article_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `layout_id` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`article_id`, `store_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$p}article_to_store` (
          `article_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`article_id`, `store_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(): void {
        $p = $this->prefix;
        $tables = [
            'article_to_store', 'article_to_layout', 'article_rating',
            'article_comment', 'article_description', 'article',
            'topic_to_store', 'topic_to_layout', 'topic_description', 'topic',
            'information_to_store', 'information_to_layout',
            'information_description', 'information',
            'banner_image', 'banner',
            'seo_url', 'translation', 'theme', 'setting',
            'layout_route', 'layout_module', 'layout',
            'antispam', 'identifier', 'upload', 'statistics',
            'notification', 'module', 'startup', 'cron', 'event',
            'modification', 'extension_path', 'extension_install', 'extension',
            'user_token', 'user_login', 'user_authorize', 'user', 'user_group',
            'session', 'language', 'store',
            'api_history', 'api_ip', 'api',
        ];
        foreach ($tables as $table) {
            $this->db->query("DROP TABLE IF EXISTS `{$p}{$table}`");
        }
    }
}
