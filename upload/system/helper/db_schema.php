<?php
/**
 * DB Create
 *
 * @param string $db_driver
 * @param string $db_hostname
 * @param string $db_username
 * @param string $db_password
 * @param string $db_database
 * @param string $db_port
 * @param string $db_prefix
 * @param string $db_ssl_key
 * @param string $db_ssl_cert
 * @param string $db_ssl_ca
 *
 * @return bool
 */
function oc_db_create(string $db_driver, string $db_hostname, string $db_username, string $db_password, string $db_database, string $db_port, string $db_prefix, string $db_ssl_key, string $db_ssl_cert, string $db_ssl_ca): bool {
	try {
		// Database
		$db = new \Opencart\System\Library\DB($db_driver, $db_hostname, $db_username, $db_password, $db_database, $db_port, $db_ssl_key, $db_ssl_cert, $db_ssl_ca);
	} catch (\Exception $e) {
		return false;
	}

	// Set up Database structure
	$tables = oc_db_schema();

	foreach ($tables as $table) {
		$table_query = $db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $db_database . "' AND TABLE_NAME = '" . $db_prefix . $table['name'] . "'");

		if ($table_query->num_rows) {
			$db->query("DROP TABLE `" . $db_prefix . $table['name'] . "`");
		}

		$sql = "CREATE TABLE `" . $db_prefix . $table['name'] . "` (" . "\n";

		foreach ($table['field'] as $field) {
			$sql .= "  `" . $field['name'] . "` " . $field['type'] . (!empty($field['not_null']) ? " NOT NULL" : "") . (isset($field['default']) ? " DEFAULT '" . $db->escape($field['default']) . "'" : "") . (!empty($field['auto_increment']) ? " AUTO_INCREMENT" : "") . ",\n";
		}

		if (isset($table['primary'])) {
			$primary_data = [];

			foreach ($table['primary'] as $primary) {
				$primary_data[] = "`" . $primary . "`";
			}

			$sql .= "  PRIMARY KEY (" . implode(",", $primary_data) . "),\n";
		}

		if (isset($table['index'])) {
			foreach ($table['index'] as $index) {
				$index_data = [];

				foreach ($index['key'] as $key) {
					$index_data[] = "`" . $key . "`";
				}

				$sql .= "  KEY `" . $index['name'] . "` (" . implode(",", $index_data) . "),\n";
			}
		}

		$sql = rtrim($sql, ",\n") . "\n";
		$sql .= ") ENGINE=" . $table['engine'] . " CHARSET=" . $table['charset'] . " COLLATE=" . $table['collate'] . ";\n";

		$db->query($sql);
	}

	return true;
}

/**
 * DB Schema
 *
 * @return array<int, array<string, mixed>>
 */
function oc_db_schema() {
	$tables = [];


	$tables[] = [
		'name'  => 'api',
		'field' => [
			[
				'name'           => 'api_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'username',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'key',
				'type' => 'text'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			],
			[
				'name' => 'date_modified',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'api_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'api_ip',
		'field' => [
			[
				'name'           => 'api_ip_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'api_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			]
		],
		'primary' => [
			'api_ip_id'
		],
		'foreign' => [
			[
				'key'   => 'api_id',
				'table' => 'api',
				'field' => 'api_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'api_history',
		'field' => [
			[
				'name'           => 'api_history_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'api_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'call',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'api_history_id'
		],
		'foreign' => [
			[
				'key'   => 'api_id',
				'table' => 'api',
				'field' => 'api_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'banner',
		'field' => [
			[
				'name'           => 'banner_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			]
		],
		'primary' => [
			'banner_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'banner_image',
		'field' => [
			[
				'name'           => 'banner_image_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'banner_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'title',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'link',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'image',
				'type' => 'varchar(255)'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'banner_image_id'
		],
		'foreign' => [
			[
				'key'   => 'banner_id',
				'table' => 'banner',
				'field' => 'banner_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'antispam',
		'field' => [
			[
				'name'           => 'antispam_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'keyword',
				'type' => 'varchar(64)'
			]
		],
		'primary' => [
			'antispam_id'
		],
		'index' => [
			[
				'name' => 'keyword',
				'key'  => [
					'keyword'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'article',
		'field' => [
			[
				'name'           => 'article_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'topic_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'author',
				'type' => 'varchar(64)'
			],
			[
				'name'    => 'rating',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			],
			[
				'name' => 'date_modified',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'article_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'article_comment',
		'field' => [
			[
				'name'           => 'article_comment_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'article_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'parent_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'customer_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'author',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'comment',
				'type' => 'text'
			],
			[
				'name'    => 'rating',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'article_comment_id'
		],
		'foreign' => [
			[
				'key'   => 'article_id',
				'table' => 'article',
				'field' => 'article_id'
			],
			[
				'key'   => 'customer_id',
				'table' => 'customer',
				'field' => 'customer_id'
			]
		],
		'index' => [
			[
				'name' => 'article_id',
				'key'  => [
					'article_id'
				]
			],
			[
				'name' => 'customer_id',
				'key'  => [
					'customer_id'
				]
			],
			[
				'name' => 'parent_id',
				'key'  => [
					'parent_id'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'article_description',
		'field' => [
			[
				'name' => 'article_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'image',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'tag',
				'type' => 'text'
			],
			[
				'name' => 'meta_title',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_description',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_keyword',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'article_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'article_rating',
		'field' => [
			[
				'name'           => 'article_rating_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'article_comment_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'article_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'customer_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'rating',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'article_rating_id'
		],
		'foreign' => [
			[
				'key'   => 'article_comment_id',
				'table' => 'article_comment',
				'field' => 'article_comment_id'
			],
			[
				'key'   => 'article_id',
				'table' => 'article',
				'field' => 'article_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'customer_id',
				'table' => 'customer',
				'field' => 'customer_id'
			]
		],
		'index' => [
			[
				'name' => 'article_comment_id',
				'key'  => [
					'article_comment_id'
				]
			],
			[
				'name' => 'article_id',
				'key'  => [
					'article_id'
				]
			],
			[
				'name' => 'store_id',
				'key'  => [
					'store_id'
				]
			],
			[
				'name' => 'customer_id',
				'key'  => [
					'customer_id'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'article_to_layout',
		'field' => [
			[
				'name' => 'article_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'layout_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'article_id',
			'store_id'
		],
		'foreign' => [
			[
				'key'   => 'article_id',
				'table' => 'article',
				'field' => 'article_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'layout_id',
				'table' => 'layout',
				'field' => 'layout_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'article_to_store',
		'field' => [
			[
				'name' => 'article_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'article_id',
			'store_id',
		],
		'foreign' => [
			[
				'key'   => 'article_id',
				'table' => 'article',
				'field' => 'article_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'topic',
		'field' => [
			[
				'name'           => 'topic_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			]
		],
		'primary' => [
			'topic_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'topic_description',
		'field' => [
			[
				'name' => 'topic_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'image',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_title',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_description',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_keyword',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'topic_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'topic_to_layout',
		'field' => [
			[
				'name' => 'topic_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'layout_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'topic_id',
			'store_id'
		],
		'foreign' => [
			[
				'key'   => 'topic_id',
				'table' => 'topic',
				'field' => 'topic_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'layout_id',
				'table' => 'layout',
				'field' => 'layout_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'topic_to_store',
		'field' => [
			[
				'name' => 'topic_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'topic_id',
			'store_id',
		],
		'foreign' => [
			[
				'key'   => 'topic_id',
				'table' => 'topic',
				'field' => 'topic_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'category',
		'field' => [
			[
				'name'           => 'category_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'image',
				'type' => 'varchar(255)'
			],
			[
				'name'    => 'parent_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			]
		],
		'primary' => [
			'category_id'
		],
		'index' => [
			[
				'name' => 'parent_id',
				'key'  => [
					'parent_id'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'category_description',
		'field' => [
			[
				'name' => 'category_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'meta_title',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_description',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_keyword',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'category_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'category_filter',
		'field' => [
			[
				'name' => 'category_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'filter_id',
				'type' => 'int(11)'
			]
		],
		'primary' => [
			'category_id',
			'filter_id'
		],
		'foreign' => [
			[
				'key'   => 'category_id',
				'table' => 'category',
				'field' => 'category_id'
			],
			[
				'key'   => 'filter_id',
				'table' => 'filter',
				'field' => 'filter_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'category_path',
		'field' => [
			[
				'name' => 'category_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'path_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'level',
				'type' => 'int(11)'
			]
		],
		'primary' => [
			'category_id',
			'path_id'
		],
		'foreign' => [
			[
				'key'   => 'category_id',
				'table' => 'category',
				'field' => 'category_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'category_to_layout',
		'field' => [
			[
				'name' => 'category_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'layout_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'category_id',
			'store_id'
		],
		'foreign' => [
			[
				'key'   => 'category_id',
				'table' => 'category',
				'field' => 'category_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'layout_id',
				'table' => 'layout',
				'field' => 'layout_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'category_to_store',
		'field' => [
			[
				'name' => 'category_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'category_id',
			'store_id',
		],
		'foreign' => [
			[
				'key'   => 'category_id',
				'table' => 'category',
				'field' => 'category_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'country',
		'field' => [
			[
				'name'           => 'country_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'iso_code_2',
				'type' => 'varchar(2)'
			],
			[
				'name' => 'iso_code_3',
				'type' => 'varchar(3)'
			],
			[
				'name'    => 'address_format_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'postcode_required',
				'type' => 'tinyint(1)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '1'
			]
		],
		'primary' => [
			'country_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'country_description',
		'field' => [
			[
				'name' => 'country_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'country_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'cron',
		'field' => [
			[
				'name'           => 'cron_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'code',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'cycle',
				'type' => 'varchar(12)'
			],
			[
				'name' => 'action',
				'type' => 'text'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			],
			[
				'name' => 'date_modified',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'cron_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'currency',
		'field' => [
			[
				'name'           => 'currency_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'title',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(3)'
			],
			[
				'name' => 'symbol_left',
				'type' => 'varchar(12)'
			],
			[
				'name' => 'symbol_right',
				'type' => 'varchar(12)'
			],
			[
				'name'    => 'decimal_place',
				'type'    => 'int(1)',
				'default' => '2'
			],
			[
				'name' => 'value',
				'type' => 'double(15,8)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_modified',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'currency_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'customer_group',
		'field' => [
			[
				'name'           => 'customer_group_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'approval',
				'type'    => 'int(1)',
				'default' => '0'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'customer_group_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'customer_group_description',
		'field' => [
			[
				'name' => 'customer_group_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			]
		],
		'primary' => [
			'customer_group_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'customer_group_id',
				'table' => 'customer_group',
				'field' => 'customer_group_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'custom_field',
		'field' => [
			[
				'name'           => 'custom_field_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'type',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'value',
				'type' => 'text'
			],
			[
				'name' => 'validation',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'location',
				'type' => 'varchar(10)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'custom_field_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'custom_field_customer_group',
		'field' => [
			[
				'name' => 'custom_field_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'customer_group_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'required',
				'type'    => 'tinyint(1)',
				'default' => '0'
			]
		],
		'primary' => [
			'custom_field_id',
			'customer_group_id'
		],
		'foreign' => [
			[
				'key'   => 'custom_field_id',
				'table' => 'custom_field',
				'field' => 'custom_field_id'
			],
			[
				'key'   => 'customer_group_id',
				'table' => 'customer_group',
				'field' => 'customer_group_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'custom_field_description',
		'field' => [
			[
				'name' => 'custom_field_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(128)'
			]
		],
		'primary' => [
			'custom_field_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'custom_field_id',
				'table' => 'custom_field',
				'field' => 'custom_field_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'custom_field_value',
		'field' => [
			[
				'name'           => 'custom_field_value_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'custom_field_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'custom_field_value_id'
		],
		'foreign' => [
			[
				'key'   => 'custom_field_id',
				'table' => 'custom_field',
				'field' => 'custom_field_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'custom_field_value_description',
		'field' => [
			[
				'name' => 'custom_field_value_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'custom_field_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(128)'
			]
		],
		'primary' => [
			'custom_field_value_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			],
			[
				'key'   => 'custom_field_id',
				'table' => 'custom_field',
				'field' => 'custom_field_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'download',
		'field' => [
			[
				'name'           => 'download_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'filename',
				'type' => 'varchar(160)'
			],
			[
				'name' => 'mask',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'download_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'download_description',
		'field' => [
			[
				'name' => 'download_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			]
		],
		'primary' => [
			'download_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'download_report',
		'field' => [
			[
				'name'           => 'download_report_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'download_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'country',
				'type' => 'varchar(2)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'download_report_id'
		],
		'foreign' => [
			[
				'key'   => 'download_id',
				'table' => 'download',
				'field' => 'download_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'event',
		'field' => [
			[
				'name'           => 'event_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'code',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'trigger',
				'type' => 'text'
			],
			[
				'name' => 'action',
				'type' => 'text'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '1'
			]
		],
		'primary' => [
			'event_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'extension',
		'field' => [
			[
				'name'           => 'extension_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'extension',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'type',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(128)'
			]
		],
		'primary' => [
			'extension_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'extension_install',
		'field' => [
			[
				'name'           => 'extension_install_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'extension_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'extension_download_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'name',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'code',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'version',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'author',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'link',
				'type' => 'varchar(255)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'extension_install_id'
		],
		'foreign' => [
			[
				'key'   => 'extension_id',
				'table' => 'extension',
				'field' => 'extension_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'extension_path',
		'field' => [
			[
				'name'           => 'extension_path_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'extension_install_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'path',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'extension_path_id'
		],
		'foreign' => [
			[
				'key'   => 'extension_install_id',
				'table' => 'extension_install',
				'field' => 'extension_install_id'
			]
		],
		'index' => [
			[
				'name' => 'path',
				'key'  => [
					'path'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'filter',
		'field' => [
			[
				'name'           => 'filter_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'filter_group_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'filter_id'
		],
		'foreign' => [
			[
				'key'   => 'filter_group_id',
				'table' => 'filter_group',
				'field' => 'filter_group_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'filter_description',
		'field' => [
			[
				'name' => 'filter_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			]
		],
		'primary' => [
			'filter_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'filter_group',
		'field' => [
			[
				'name'           => 'filter_group_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'filter_group_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'filter_group_description',
		'field' => [
			[
				'name' => 'filter_group_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			]
		],
		'primary' => [
			'filter_group_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'filter_group_id',
				'table' => 'filter_group',
				'field' => 'filter_group_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'gdpr',
		'field' => [
			[
				'name'           => 'gdpr_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'email',
				'type' => 'varchar(96)'
			],
			[
				'name' => 'action',
				'type' => 'varchar(6)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'gdpr_id'
		],
		'foreign' => [
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'geo_zone',
		'field' => [
			[
				'name'           => 'geo_zone_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'description',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'geo_zone_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'identifier',
		'field' => [
			[
				'name'           => 'identifier_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(48)'
			],
			[
				'name' => 'validation',
				'type' => 'varchar(255)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			]
		],
		'primary' => [
			'identifier_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'information',
		'field' => [
			[
				'name'           => 'information_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '1'
			]
		],
		'primary' => [
			'information_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'information_description',
		'field' => [
			[
				'name' => 'information_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'title',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'description',
				'type' => 'mediumtext'
			],
			[
				'name' => 'meta_title',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_description',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'meta_keyword',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'information_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'information_to_layout',
		'field' => [
			[
				'name' => 'information_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name'    => 'layout_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'information_id',
			'store_id'
		],
		'foreign' => [
			[
				'key'   => 'information_id',
				'table' => 'information',
				'field' => 'information_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'layout_id',
				'table' => 'layout',
				'field' => 'layout_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'information_to_store',
		'field' => [
			[
				'name' => 'information_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'information_id',
			'store_id'
		],
		'foreign' => [
			[
				'key'   => 'information_id',
				'table' => 'information',
				'field' => 'information_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'language',
		'field' => [
			[
				'name'           => 'language_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(5)'
			],
			[
				'name' => 'locale',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'extension',
				'type' => 'varchar(255)'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			]
		],
		'primary' => [
			'language_id'
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'layout',
		'field' => [
			[
				'name'           => 'layout_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			]
		],
		'primary' => [
			'layout_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'layout_module',
		'field' => [
			[
				'name'           => 'layout_module_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'layout_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'code',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'position',
				'type' => 'varchar(14)'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'layout_module_id'
		],
		'foreign' => [
			[
				'key'   => 'layout_id',
				'table' => 'layout',
				'field' => 'layout_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'layout_route',
		'field' => [
			[
				'name'           => 'layout_route_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'layout_id',
				'type' => 'int(11)',
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'route',
				'type' => 'varchar(64)'
			]
		],
		'primary' => [
			'layout_route_id'
		],
		'foreign' => [
			[
				'key'   => 'layout_id',
				'table' => 'layout',
				'field' => 'layout_id'
			],
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'length_class',
		'field' => [
			[
				'name'           => 'length_class_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'value',
				'type' => 'decimal(15,8)'
			]
		],
		'primary' => [
			'length_class_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'length_class_description',
		'field' => [
			[
				'name' => 'length_class_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'title',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'unit',
				'type' => 'varchar(4)'
			]
		],
		'primary' => [
			'length_class_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'length_class_id',
				'table' => 'length_class',
				'field' => 'length_class_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'location',
		'field' => [
			[
				'name'           => 'location_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'address',
				'type' => 'text'
			],
			[
				'name' => 'telephone',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'geocode',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'image',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'open',
				'type' => 'text'
			],
			[
				'name' => 'comment',
				'type' => 'text'
			]
		],
		'primary' => [
			'location_id'
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'modification',
		'field' => [
			[
				'name'           => 'modification_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'     => 'extension_install_id',
				'type'     => 'int(11)',
				'not_null' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'code',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'author',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'version',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'link',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'xml',
				'type' => 'mediumtext'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'modification_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'module',
		'field' => [
			[
				'name'           => 'module_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'setting',
				'type' => 'text'
			]
		],
		'primary' => [
			'module_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'notification',
		'field' => [
			[
				'name'           => 'notification_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'title',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'text',
				'type' => 'text'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(11)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'notification_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'startup',
		'field' => [
			[
				'name'           => 'startup_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'code',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'action',
				'type' => 'text'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'startup_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'statistics',
		'field' => [
			[
				'name'           => 'statistics_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'code',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'value',
				'type' => 'decimal(15,4)'
			]
		],
		'primary' => [
			'statistics_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'session',
		'field' => [
			[
				'name' => 'session_id',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'data',
				'type' => 'text'
			],
			[
				'name' => 'expire',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'session_id'
		],
		'index' => [
			[
				'name' => 'expire',
				'key'  => [
					'expire'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'setting',
		'field' => [
			[
				'name'           => 'setting_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'code',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'key',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'value',
				'type' => 'text'
			],
			[
				'name'    => 'serialized',
				'type'    => 'tinyint(1)',
				'default' => '0'
			]
		],
		'primary' => [
			'setting_id'
		],
		'foreign' => [
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'store',
		'field' => [
			[
				'name'           => 'store_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'url',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'store_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'theme',
		'field' => [
			[
				'name'           => 'theme_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'route',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'code',
				'type' => 'mediumtext'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'theme_id'
		],
		'foreign' => [
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'translation',
		'field' => [
			[
				'name'           => 'translation_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'route',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'key',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'value',
				'type' => 'text'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'translation_id'
		],
		'foreign' => [
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'upload',
		'field' => [
			[
				'name'           => 'upload_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'filename',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'upload_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'seo_url',
		'field' => [
			[
				'name'           => 'seo_url_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'key',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'value',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'keyword',
				'type' => 'varchar(768)'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			]
		],
		'primary' => [
			'seo_url_id'
		],
		'foreign' => [
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'index' => [
			[
				'name' => 'store',
				'key'  => [
					'store_id'
				]
			],
			[
				'name' => 'language',
				'key'  => [
					'language_id'
				]
			],
			[
				'name' => 'keyword',
				'key'  => [
					'keyword'
				]
			],
			[
				'name' => 'query',
				'key'  => [
					'key',
					'value'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'user',
		'field' => [
			[
				'name'           => 'user_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'user_group_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'username',
				'type' => 'varchar(20)'
			],
			[
				'name' => 'password',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'firstname',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'lastname',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'email',
				'type' => 'varchar(96)'
			],
			[
				'name'    => 'image',
				'type'    => 'varchar(255)',
				'default' => ''
			],
			[
				'name'    => 'ip',
				'type'    => 'varchar(40)',
				'default' => ''
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'user_id'
		],
		'foreign' => [
			[
				'key'   => 'user_group_id',
				'table' => 'user_group',
				'field' => 'user_group_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'user_authorize',
		'field' => [
			[
				'name'           => 'user_authorize_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'user_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'token',
				'type' => 'varchar(96)'
			],
			[
				'name'    => 'total',
				'type'    => 'int(1)',
				'default' => '0'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'user_agent',
				'type' => 'varchar(255)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			],
			[
				'name' => 'date_expire',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'user_authorize_id'
		],
		'foreign' => [
			[
				'key'   => 'user_id',
				'table' => 'user',
				'field' => 'user_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'user_group',
		'field' => [
			[
				'name'           => 'user_group_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'permission',
				'type' => 'text'
			]
		],
		'primary' => [
			'user_group_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'user_login',
		'field' => [
			[
				'name'           => 'user_login_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'user_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'user_agent',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'user_login_id'
		],
		'foreign' => [
			[
				'key'   => 'user_id',
				'table' => 'user',
				'field' => 'user_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'user_token',
		'field' => [
			[
				'name'           => 'user_token_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'user_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'code',
				'type' => 'text'
			],
			[
				'name' => 'type',
				'type' => 'varchar(10)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'user_token_id'
		],
		'foreign' => [
			[
				'key'   => 'user_id',
				'table' => 'user',
				'field' => 'user_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'weight_class',
		'field' => [
			[
				'name'           => 'weight_class_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'value',
				'type'    => 'decimal(15,8)',
				'default' => '0.00000000'
			]
		],
		'primary' => [
			'weight_class_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'weight_class_description',
		'field' => [
			[
				'name' => 'weight_class_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'title',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'unit',
				'type' => 'varchar(4)'
			]
		],
		'primary' => [
			'weight_class_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'zone',
		'field' => [
			[
				'name'           => 'zone_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'country_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(32)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '1'
			]
		],
		'primary' => [
			'zone_id'
		],
		'foreign' => [
			[
				'key'   => 'country_id',
				'table' => 'country',
				'field' => 'country_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'zone_description',
		'field' => [
			[
				'name' => 'zone_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'zone_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	$tables[] = [
		'name'  => 'zone_to_geo_zone',
		'field' => [
			[
				'name'           => 'zone_to_geo_zone_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'geo_zone_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'country_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'zone_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'zone_to_geo_zone_id'
		],
		'foreign' => [
			[
				'key'   => 'geo_zone_id',
				'table' => 'geo_zone',
				'field' => 'geo_zone_id'
			],
			[
				'key'   => 'country_id',
				'table' => 'country',
				'field' => 'country_id'
			],
			[
				'key'   => 'zone_id',
				'table' => 'zone',
				'field' => 'zone_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];


	return $tables;
}
