INSERT INTO `oc_address_format` (`address_format_id`, `name`, `address_format`)
INSERT INTO `oc_country` (`country_id`, `iso_code_2`, `iso_code_3`, `address_format_id`, `postcode_required`, `status`)
INSERT INTO `oc_country_description` (`country_id`, `language_id`, `name`)
INSERT INTO `oc_cron` (`code`, `description`, `cycle`, `action`, `status`, `date_added`, `date_modified`)
INSERT INTO `oc_currency` (`title`, `code`, `symbol_left`, `symbol_right`, `decimal_place`, `value`, `status`, `date_modified`)
INSERT INTO `oc_customer_group` (`customer_group_id`, `approval`, `sort_order`)
INSERT INTO `oc_customer_group_description` (`customer_group_id`, `language_id`, `name`, `description`)
INSERT INTO `oc_event` (`code`, `description`, `trigger`, `action`, `status`)
INSERT INTO `oc_extension` (`extension`, `type`, `code`)
INSERT INTO `oc_extension_install` (`extension_install_id`, `extension_id`, `extension_download_id`, `name`, `description`, `code`, `version`, `author`, `link`, `status`, `date_added`)
INSERT INTO `oc_extension_path` (`extension_install_id`, `path`)
INSERT INTO `oc_geo_zone` (`geo_zone_id`, `name`, `description`)
INSERT INTO `oc_information` (`information_id`, `sort_order`, `status`)
INSERT INTO `oc_information_description` (`information_id`, `language_id`, `title`, `description`, `meta_title`, `meta_description`, `meta_keyword`)
INSERT INTO `oc_information_to_store` (`information_id`, `store_id`)
INSERT INTO `oc_language` (`language_id`, `name`, `code`, `locale`, `sort_order`, `status`)
INSERT INTO `oc_layout` (`layout_id`, `name`)
INSERT INTO `oc_layout_module` (`layout_module_id`, `layout_id`, `code`, `position`, `sort_order`)
INSERT INTO `oc_layout_route` (`layout_route_id`, `layout_id`, `store_id`, `route`)
INSERT INTO `oc_length_class` (`length_class_id`, `value`)
INSERT INTO `oc_length_class_description` (`length_class_id`, `language_id`, `title`, `unit`)
INSERT INTO `oc_module` (`module_id`, `name`, `code`, `setting`)
INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`)
INSERT INTO `oc_seo_url` (`store_id`, `language_id`, `key`, `value`, `keyword`, `sort_order`)
INSERT INTO `oc_statistics` (`statistics_id`, `code`, `value`)
INSERT INTO `oc_stock_status` (`stock_status_id`, `language_id`, `name`)
INSERT INTO `oc_tax_class` (`tax_class_id`, `title`, `description`)
INSERT INTO `oc_tax_rate` (`tax_rate_id`, `geo_zone_id`, `name`, `rate`, `type`)
INSERT INTO `oc_tax_rate_to_customer_group` (`tax_rate_id`, `customer_group_id`)
INSERT INTO `oc_tax_rule` (`tax_rule_id`, `tax_class_id`, `tax_rate_id`, `based`, `priority`)
INSERT INTO `oc_user_group` (`user_group_id`, `name`, `permission`)
INSERT INTO `oc_weight_class` (`weight_class_id`, `value`)
INSERT INTO `oc_weight_class_description` (`weight_class_id`, `language_id`, `title`, `unit`)
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
INSERT INTO `oc_zone_to_geo_zone` (`zone_to_geo_zone_id`, `country_id`, `zone_id`, `geo_zone_id`)
INSERT INTO `oc_address_format` (`address_format_id`, `name`, `address_format`)
VALUES (1, 'Address Format', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}');
INSERT INTO `oc_country` (`country_id`, `iso_code_2`, `iso_code_3`, `address_format_id`, `postcode_required`, `status`)
VALUES (1, 'AF', 'AFG', 1, 0, 1),
       (2, 'AL', 'ALB', 1, 0, 1),
INSERT INTO `oc_country_description` (`country_id`, `language_id`, `name`)
VALUES (1, 1, 'Afghanistan'),
       (2, 1, 'Albania'),
INSERT INTO `oc_cron` (`code`, `description`, `cycle`, `action`, `status`, `date_added`, `date_modified`)
VALUES ('currency', 'Updates currency conversion values.', 'day', 'cron/currency', 1, '2014-09-25 14:40:00', '2014-09-25 14:40:00'),
       ('gdpr', 'Deletes and send emails to customers who have requested their GPDR data to be deleted.', 'day', 'cron/gdpr', 1, '2014-09-25 14:40:00', '2014-09-25 14:40:00'),
INSERT INTO `oc_currency` (`title`, `code`, `symbol_left`, `symbol_right`, `decimal_place`, `value`, `status`, `date_modified`)
VALUES ('Pound Sterling', 'GBP', '£', '', '2', 0.61250001, 1, '2014-09-25 14:40:00'),
       ('US Dollar', 'USD', '$', '', '2', 1.00000000, 1, '2014-09-25 14:40:00'),
INSERT INTO `oc_customer_group` (`customer_group_id`, `approval`, `sort_order`)
VALUES (1, 0, 1),
       (2, 0, 2),
INSERT INTO `oc_customer_group_description` (`customer_group_id`, `language_id`, `name`, `description`)
VALUES (1, 1, 'Default', 'Default customer group'),
       (2, 1, 'Retail', 'Retail customers'),
INSERT INTO `oc_event` (`code`, `description`, `trigger`, `action`, `status`)
VALUES ('activity_customer_add', 'Adds new customer entry in the activity log.', 'catalog/model/account/customer.addCustomer/after', 'event/activity.addCustomer', 1),
       ('activity_customer_edit', 'Adds edit customer entry in the activity log.', 'catalog/model/account/customer.editCustomer/after', 'event/activity.editCustomer', 1),
INSERT INTO `oc_extension` (`extension`, `type`, `code`)
VALUES ('opencart', 'currency', 'ecb'),
       ('opencart', 'module', 'featured'),
INSERT INTO `oc_extension_install` (`extension_install_id`, `extension_id`, `extension_download_id`, `name`, `description`, `code`, `version`, `author`, `link`, `status`, `date_added`)
VALUES (1, 0, 0, 'OpenCart Default Extensions', 'This extension contains all the default extensions for modules, currencies, payment methods, shipping methods, anti-fraud, themes, order totals and reports.', 'opencart', '1.0', 'OpenCart Ltd', 'http://www.opencart.com', 1, '2020-08-29 15:35:39');
INSERT INTO `oc_extension_path` (`extension_install_id`, `path`)
VALUES (1, 'opencart'),
       (1, 'opencart/admin'),
INSERT INTO `oc_geo_zone` (`geo_zone_id`, `name`, `description`)
VALUES (3, 'UK VAT Zone', 'UK VAT'),
       (4, 'UK Shipping', 'UK Shipping Zones');
INSERT INTO `oc_identifier` (`name`, `code`, `status`)
VALUES ('Stock Keeping Unit', 'SKU', 1),
       ('Universal Product Code', 'UPC', 1),
INSERT INTO `oc_information` (`information_id`, `sort_order`, `status`)
VALUES (1, 3, 1),
       (2, 1, 1),
INSERT INTO `oc_information_description` (`information_id`, `language_id`, `title`, `description`, `meta_title`, `meta_description`, `meta_keyword`)
VALUES (1, 1, 'About Us', '&lt;p&gt;\r\n	About Us&lt;/p&gt;\r\n', 'About Us', '', ''),
       (2, 1, 'Terms &amp; Conditions', '&lt;p&gt;\r\n	Terms &amp;amp; Conditions&lt;/p&gt;\r\n', 'Terms &amp; Conditions', '', ''),
INSERT INTO `oc_information_to_store` (`information_id`, `store_id`)
VALUES (1, 0),
       (2, 0),
INSERT INTO `oc_language` (`language_id`, `name`, `code`, `locale`, `sort_order`, `status`)
VALUES (1, 'English', 'en-gb', 'en-gb,en', 1, 1);
INSERT INTO `oc_layout` (`layout_id`, `name`)
VALUES (1, 'Home'),
       (2, 'Product'),
INSERT INTO `oc_layout_module` (`layout_module_id`, `layout_id`, `code`, `position`, `sort_order`)
VALUES (1, 10, 'opencart.account', 'column_right', 1),
       (2, 6, 'opencart.account', 'column_right', 1),
INSERT INTO `oc_layout_route` (`layout_route_id`, `layout_id`, `store_id`, `route`)
VALUES (1, 6, 0, 'account/%'),
       (2, 6, 0, 'information/gdpr'),
INSERT INTO `oc_length_class` (`length_class_id`, `value`)
VALUES (1, '1.00000000'),
       (2, '10.00000000'),
INSERT INTO `oc_length_class_description` (`length_class_id`, `language_id`, `title`, `unit`)
VALUES (1, 1, 'Centimeter', 'cm'),
       (2, 1, 'Millimeter', 'mm'),
INSERT INTO `oc_module` (`module_id`, `name`, `code`, `setting`)
VALUES (1, 'Category Banner', 'opencart.banner', '{"name":"Category Banner","banner_id":"6","effect":"fade","items":"1","controls":"0","indicators":"0","interval":"5000","width":"200","height":"180","status":"1"}'),
       (2, 'Featured', 'opencart.featured', '{"name":"Featured","product_name":"","product":["43","40","42","30"],"axis":"horizontal","limit":"4","width":"200","height":"200","status":"1"}'),
INSERT INTO `oc_seo_url` (`store_id`, `language_id`, `key`, `value`, `keyword`, `sort_order`)
VALUES (0, 1, 'product_id', '47', 'hp-lp3065', 1),
       (0, 1, 'product_id', '48', 'ipod-classic', 1),
INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`)
VALUES (0, 'config', 'config_shared', '0', 0),
       (0, 'config', 'config_fraud_detection', '0', 0),
INSERT INTO `oc_statistics` (`statistics_id`, `code`, `value`)
VALUES (1, 'order_sale', 0),
       (2, 'order_processing', 0),
INSERT INTO `oc_stock_status` (`stock_status_id`, `language_id`, `name`)
VALUES (7, 1, 'In Stock'),
       (8, 1, 'Pre-Order'),
INSERT INTO `oc_user_group` (`user_group_id`, `name`, `permission`)
VALUES (1, 'Administrator', '{\"access\":[\"catalog\\/attribute\",\"catalog\\/attribute_group\",\"catalog\\/category\",\"catalog\\/download\",\"catalog\\/filter\",\"catalog\\/filter_group\",\"catalog\\/identifier\",\"catalog\\/information\",\"catalog\\/manufacturer\",\"catalog\\/option\",\"catalog\\/product\",\"catalog\\/review\",\"catalog\\/subscription_plan\",\"cms\\/antispam\",\"cms\\/article\",\"cms\\/comment\",\"cms\\/topic\",\"common\\/developer\",\"common\\/filemanager\",\"common\\/security\",\"customer\\/address\",\"customer\\/custom_field\",\"customer\\/customer\",\"customer\\/customer_approval\",\"customer\\/customer_group\",\"customer\\/gdpr\",\"design\\/banner\",\"design\\/layout\",\"design\\/seo_url\",\"design\\/theme\",\"design\\/translation\",\"error\\/exception\",\"event\\/modification\",\"extension\\/analytics\",\"extension\\/captcha\",\"extension\\/currency\",\"extension\\/dashboard\",\"extension\\/feed\",\"extension\\/fraud\",\"extension\\/language\",\"extension\\/marketplace\",\"extension\\/module\",\"extension\\/other\",\"extension\\/payment\",\"extension\\/report\",\"extension\\/shipping\",\"extension\\/theme\",\"extension\\/total\",\"localisation\\/address_format\",\"localisation\\/country\",\"localisation\\/currency\",\"localisation\\/geo_zone\",\"localisation\\/language\",\"localisation\\/length_class\",\"localisation\\/location\",\"localisation\\/order_status\",\"localisation\\/return_action\",\"localisation\\/return_reason\",\"localisation\\/return_status\",\"localisation\\/stock_status\",\"localisation\\/subscription_status\",\"localisation\\/tax_class\",\"localisation\\/tax_rate\",\"localisation\\/weight_class\",\"localisation\\/zone\",\"mail\\/affiliate\",\"mail\\/authorize\",\"mail\\/customer\",\"mail\\/forgotten\",\"mail\\/gdpr\",\"mail\\/returns\",\"mail\\/reward\",\"mail\\/subscription\",\"mail\\/transaction\",\"marketing\\/affiliate\",\"marketing\\/contact\",\"marketing\\/coupon\",\"marketing\\/marketing\",\"marketplace\\/api\",\"marketplace\\/cron\",\"marketplace\\/event\",\"marketplace\\/extension\",\"marketplace\\/installer\",\"marketplace\\/marketplace\",\"marketplace\\/modification\",\"marketplace\\/promotion\",\"marketplace\\/startup\",\"report\\/online\",\"report\\/report\",\"report\\/statistics\",\"sale\\/order\",\"sale\\/returns\",\"sale\\/subscription\",\"setting\\/setting\",\"setting\\/store\",\"tool\\/backup\",\"tool\\/log\",\"tool\\/notification\",\"tool\\/upgrade\",\"tool\\/upload\",\"user\\/api\",\"user\\/profile\",\"user\\/user\",\"user\\/user_permission\",\"extension\\/opencart\\/api\\/coupon\",\"extension\\/opencart\\/api\\/reward\",\"extension\\/opencart\\/captcha\\/basic\",\"extension\\/opencart\\/currency\\/ecb\",\"extension\\/opencart\\/currency\\/fixer\",\"extension\\/opencart\\/dashboard\\/activity\",\"extension\\/opencart\\/dashboard\\/chart\",\"extension\\/opencart\\/dashboard\\/customer\",\"extension\\/opencart\\/dashboard\\/map\",\"extension\\/opencart\\/dashboard\\/online\",\"extension\\/opencart\\/dashboard\\/order\",\"extension\\/opencart\\/dashboard\\/recent\",\"extension\\/opencart\\/dashboard\\/sale\",\"extension\\/opencart\\/fraud\\/ddos\",\"extension\\/opencart\\/fraud\\/ip\",\"extension\\/opencart\\/module\\/account\",\"extension\\/opencart\\/module\\/banner\",\"extension\\/opencart\\/module\\/bestseller\",\"extension\\/opencart\\/module\\/blog\",\"extension\\/opencart\\/module\\/category\",\"extension\\/opencart\\/module\\/featured\",\"extension\\/opencart\\/module\\/filter\",\"extension\\/opencart\\/module\\/html\",\"extension\\/opencart\\/module\\/information\",\"extension\\/opencart\\/module\\/latest\",\"extension\\/opencart\\/module\\/special\",\"extension\\/opencart\\/module\\/store\",\"extension\\/opencart\\/module\\/topic\",\"extension\\/opencart\\/payment\\/bank_transfer\",\"extension\\/opencart\\/payment\\/cheque\",\"extension\\/opencart\\/payment\\/cod\",\"extension\\/opencart\\/payment\\/free_checkout\",\"extension\\/opencart\\/report\\/customer\",\"extension\\/opencart\\/report\\/customer_activity\",\"extension\\/opencart\\/report\\/customer_order\",\"extension\\/opencart\\/report\\/customer_reward\",\"extension\\/opencart\\/report\\/customer_search\",\"extension\\/opencart\\/report\\/customer_transaction\",\"extension\\/opencart\\/report\\/marketing\",\"extension\\/opencart\\/report\\/product_purchased\",\"extension\\/opencart\\/report\\/product_viewed\",\"extension\\/opencart\\/report\\/sale_coupon\",\"extension\\/opencart\\/report\\/sale_order\",\"extension\\/opencart\\/report\\/sale_return\",\"extension\\/opencart\\/report\\/sale_shipping\",\"extension\\/opencart\\/report\\/sale_tax\",\"extension\\/opencart\\/report\\/subscription\",\"extension\\/opencart\\/shipping\\/flat\",\"extension\\/opencart\\/shipping\\/free\",\"extension\\/opencart\\/shipping\\/item\",\"extension\\/opencart\\/shipping\\/pickup\",\"extension\\/opencart\\/shipping\\/weight\",\"extension\\/opencart\\/theme\\/basic\",\"extension\\/opencart\\/total\\/coupon\",\"extension\\/opencart\\/total\\/credit\",\"extension\\/opencart\\/total\\/handling\",\"extension\\/opencart\\/total\\/low_order_fee\",\"extension\\/opencart\\/total\\/reward\",\"extension\\/opencart\\/total\\/shipping\",\"extension\\/opencart\\/total\\/sub_total\",\"extension\\/opencart\\/total\\/tax\",\"extension\\/opencart\\/total\\/total\"],\"modify\":[\"catalog\\/attribute\",\"catalog\\/attribute_group\",\"catalog\\/category\",\"catalog\\/download\",\"catalog\\/filter\",\"catalog\\/filter_group\",\"catalog\\/identifier\",\"catalog\\/information\",\"catalog\\/manufacturer\",\"catalog\\/option\",\"catalog\\/product\",\"catalog\\/review\",\"catalog\\/subscription_plan\",\"cms\\/antispam\",\"cms\\/article\",\"cms\\/comment\",\"cms\\/topic\",\"common\\/developer\",\"common\\/filemanager\",\"common\\/security\",\"customer\\/address\",\"customer\\/custom_field\",\"customer\\/customer\",\"customer\\/customer_approval\",\"customer\\/customer_group\",\"customer\\/gdpr\",\"design\\/banner\",\"design\\/layout\",\"design\\/seo_url\",\"design\\/theme\",\"design\\/translation\",\"error\\/exception\",\"event\\/modification\",\"extension\\/analytics\",\"extension\\/captcha\",\"extension\\/currency\",\"extension\\/dashboard\",\"extension\\/feed\",\"extension\\/fraud\",\"extension\\/language\",\"extension\\/marketplace\",\"extension\\/module\",\"extension\\/other\",\"extension\\/payment\",\"extension\\/report\",\"extension\\/shipping\",\"extension\\/theme\",\"extension\\/total\",\"localisation\\/address_format\",\"localisation\\/country\",\"localisation\\/currency\",\"localisation\\/geo_zone\",\"localisation\\/language\",\"localisation\\/length_class\",\"localisation\\/location\",\"localisation\\/order_status\",\"localisation\\/return_action\",\"localisation\\/return_reason\",\"localisation\\/return_status\",\"localisation\\/stock_status\",\"localisation\\/subscription_status\",\"localisation\\/tax_class\",\"localisation\\/tax_rate\",\"localisation\\/weight_class\",\"localisation\\/zone\",\"mail\\/affiliate\",\"mail\\/authorize\",\"mail\\/customer\",\"mail\\/forgotten\",\"mail\\/gdpr\",\"mail\\/returns\",\"mail\\/reward\",\"mail\\/subscription\",\"mail\\/transaction\",\"marketing\\/affiliate\",\"marketing\\/contact\",\"marketing\\/coupon\",\"marketing\\/marketing\",\"marketplace\\/api\",\"marketplace\\/cron\",\"marketplace\\/event\",\"marketplace\\/extension\",\"marketplace\\/installer\",\"marketplace\\/marketplace\",\"marketplace\\/modification\",\"marketplace\\/promotion\",\"marketplace\\/startup\",\"report\\/online\",\"report\\/report\",\"report\\/statistics\",\"sale\\/order\",\"sale\\/returns\",\"sale\\/subscription\",\"setting\\/setting\",\"setting\\/store\",\"tool\\/backup\",\"tool\\/log\",\"tool\\/notification\",\"tool\\/upgrade\",\"tool\\/upload\",\"user\\/api\",\"user\\/profile\",\"user\\/user\",\"user\\/user_permission\",\"extension\\/opencart\\/api\\/coupon\",\"extension\\/opencart\\/api\\/reward\",\"extension\\/opencart\\/captcha\\/basic\",\"extension\\/opencart\\/currency\\/ecb\",\"extension\\/opencart\\/currency\\/fixer\",\"extension\\/opencart\\/dashboard\\/activity\",\"extension\\/opencart\\/dashboard\\/chart\",\"extension\\/opencart\\/dashboard\\/customer\",\"extension\\/opencart\\/dashboard\\/map\",\"extension\\/opencart\\/dashboard\\/online\",\"extension\\/opencart\\/dashboard\\/order\",\"extension\\/opencart\\/dashboard\\/recent\",\"extension\\/opencart\\/dashboard\\/sale\",\"extension\\/opencart\\/fraud\\/ddos\",\"extension\\/opencart\\/fraud\\/ip\",\"extension\\/opencart\\/module\\/account\",\"extension\\/opencart\\/module\\/banner\",\"extension\\/opencart\\/module\\/bestseller\",\"extension\\/opencart\\/module\\/blog\",\"extension\\/opencart\\/module\\/category\",\"extension\\/opencart\\/module\\/featured\",\"extension\\/opencart\\/module\\/filter\",\"extension\\/opencart\\/module\\/html\",\"extension\\/opencart\\/module\\/information\",\"extension\\/opencart\\/module\\/latest\",\"extension\\/opencart\\/module\\/special\",\"extension\\/opencart\\/module\\/store\",\"extension\\/opencart\\/module\\/topic\",\"extension\\/opencart\\/payment\\/bank_transfer\",\"extension\\/opencart\\/payment\\/cheque\",\"extension\\/opencart\\/payment\\/cod\",\"extension\\/opencart\\/payment\\/free_checkout\",\"extension\\/opencart\\/report\\/customer\",\"extension\\/opencart\\/report\\/customer_activity\",\"extension\\/opencart\\/report\\/customer_order\",\"extension\\/opencart\\/report\\/customer_reward\",\"extension\\/opencart\\/report\\/customer_search\",\"extension\\/opencart\\/report\\/customer_transaction\",\"extension\\/opencart\\/report\\/marketing\",\"extension\\/opencart\\/report\\/product_purchased\",\"extension\\/opencart\\/report\\/product_viewed\",\"extension\\/opencart\\/report\\/sale_coupon\",\"extension\\/opencart\\/report\\/sale_order\",\"extension\\/opencart\\/report\\/sale_return\",\"extension\\/opencart\\/report\\/sale_shipping\",\"extension\\/opencart\\/report\\/sale_tax\",\"extension\\/opencart\\/report\\/subscription\",\"extension\\/opencart\\/shipping\\/flat\",\"extension\\/opencart\\/shipping\\/free\",\"extension\\/opencart\\/shipping\\/item\",\"extension\\/opencart\\/shipping\\/pickup\",\"extension\\/opencart\\/shipping\\/weight\",\"extension\\/opencart\\/theme\\/basic\",\"extension\\/opencart\\/total\\/coupon\",\"extension\\/opencart\\/total\\/credit\",\"extension\\/opencart\\/total\\/handling\",\"extension\\/opencart\\/total\\/low_order_fee\",\"extension\\/opencart\\/total\\/reward\",\"extension\\/opencart\\/total\\/shipping\",\"extension\\/opencart\\/total\\/sub_total\",\"extension\\/opencart\\/total\\/tax\",\"extension\\/opencart\\/total\\/total\"]}'),
       (2, 'Demonstration', ''),
INSERT INTO `oc_weight_class` (`weight_class_id`, `value`)
VALUES (1, '1.00000000'),
       (2, '1000.00000000'),
INSERT INTO `oc_weight_class_description` (`weight_class_id`, `language_id`, `title`, `unit`)
VALUES (1, 1, 'Kilogram', 'kg'),
       (2, 1, 'Gram', 'g'),
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
VALUES (1, 1, 'BDS', 1),
       (2, 1, 'BDG', 1),
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
VALUES (1557, 101, 'HRM', 1),
       (1558, 101, 'SBL', 1),
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
VALUES (3093, 203, 'U', 1),
       (3094, 203, 'O', 1),
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
VALUES (4446, 97, 'HU-BA', 1),
       (4447, 97, 'HU-BK', 1),
INSERT INTO `oc_zone` (`zone_id`, `country_id`, `code`, `status`)
VALUES (4466, 126, 'MK001', 1),
       (4467, 126, 'MK002', 1),
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
VALUES (1, 1, 'Badakhshan'),
       (2, 1, 'Badghis'),
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
VALUES (1557, 1, 'Hormozgan'),
       (1558, 1, 'Sistan and Baluchistan'),
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
VALUES (3093, 1, 'Västmanland'),
       (3094, 1, 'Västra Götaland'),
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
VALUES (4446, 1, 'Baranya'),
       (4447, 1, 'Bács-Kiskun'),
INSERT INTO `oc_zone_description` (`zone_id`, `language_id`, `name`)
VALUES (4466, 1, 'Vardarski'),
       (4467, 1, 'Istočen'),
INSERT INTO `oc_zone_to_geo_zone` (`zone_to_geo_zone_id`, `country_id`, `zone_id`, `geo_zone_id`)
VALUES (1, 222, 0, 4),
       (2, 222, 3513, 3),
