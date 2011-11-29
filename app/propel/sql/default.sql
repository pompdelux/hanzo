
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- categories
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER,
	`context` VARCHAR(32) DEFAULT '',
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `index2` (`context`),
	INDEX `categories_FI_1` (`parent_id`),
	CONSTRAINT `categories_FK_1`
		FOREIGN KEY (`parent_id`)
		REFERENCES `categories` (`id`)
		ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- cms_thread
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cms_thread`;

CREATE TABLE `cms_thread`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- cms
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cms`;

CREATE TABLE `cms`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER,
	`cms_thread_id` INTEGER NOT NULL,
	`sort` INTEGER DEFAULT 1 NOT NULL,
	`type` VARCHAR(255) DEFAULT 'cms' NOT NULL,
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `FI_cms_1` (`cms_thread_id`),
	INDEX `FI_cms_2` (`parent_id`),
	CONSTRAINT `fk_cms_1`
		FOREIGN KEY (`cms_thread_id`)
		REFERENCES `cms_thread` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `fk_cms_2`
		FOREIGN KEY (`parent_id`)
		REFERENCES `cms` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- consultants_info
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `consultants_info`;

CREATE TABLE `consultants_info`
(
	`consultants_id` INTEGER NOT NULL,
	`description` TEXT,
	`max_notified` TINYINT(1) DEFAULT 0 NOT NULL,
	`latitude` FLOAT(10,6),
	`longitude` FLOAT(10,6),
	PRIMARY KEY (`consultants_id`),
	INDEX `index2` (`consultants_id`),
	INDEX `index3` (`latitude`, `longitude`),
	CONSTRAINT `fk_consultants_info_1`
		FOREIGN KEY (`consultants_id`)
		REFERENCES `customers` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- countries
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(128) NOT NULL,
	`local_name` VARCHAR(128) NOT NULL,
	`code` INTEGER,
	`iso2` VARCHAR(2) NOT NULL,
	`iso3` VARCHAR(3) NOT NULL,
	`continent` VARCHAR(2) NOT NULL,
	`currency_id` INTEGER NOT NULL,
	`curency_code` VARCHAR(3) NOT NULL,
	`curerncy_name` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `index2` (`code`),
	UNIQUE INDEX `index3` (`iso2`),
	UNIQUE INDEX `index4` (`iso3`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- coupons
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `coupons`;

CREATE TABLE `coupons`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`code` VARCHAR(12) NOT NULL,
	`amount` DECIMAL(15,4) NOT NULL,
	`vat` DECIMAL(2,2),
	`currency_code` VARCHAR(3) NOT NULL,
	`uses_pr_coupon` INTEGER DEFAULT 1 NOT NULL,
	`uses_pr_coustomer` INTEGER DEFAULT 1 NOT NULL,
	`active_from` DATETIME,
	`active_to` DATETIME,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `code_UNIQUE` (`code`),
	INDEX `index3` (`currency_code`),
	INDEX `index4` (`code`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- coupons_to_customers
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `coupons_to_customers`;

CREATE TABLE `coupons_to_customers`
(
	`coupons_id` INTEGER NOT NULL,
	`customers_id` INTEGER NOT NULL,
	PRIMARY KEY (`coupons_id`,`customers_id`),
	INDEX `FI_coupons_to_customers_1` (`customers_id`),
	CONSTRAINT `fk_coupons_to_customers_1`
		FOREIGN KEY (`customers_id`)
		REFERENCES `customers` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `fk_coupons_to_customers_2`
		FOREIGN KEY (`coupons_id`)
		REFERENCES `coupons` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customers
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(128) NOT NULL,
	`last_name` VARCHAR(128) NOT NULL,
	`initials` VARCHAR(6),
	`password` VARCHAR(128) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`phone` VARCHAR(32),
	`password_clear` VARCHAR(45),
	`billing_address_line_1` VARCHAR(255),
	`billing_address_line_2` VARCHAR(255),
	`billing_postal_code` VARCHAR(12),
	`billing_city` VARCHAR(64),
	`billing_country` VARCHAR(128),
	`billing_countries_id` INTEGER,
	`billing_state_province` VARCHAR(64),
	`delivery_address_line_1` VARCHAR(255),
	`delivery_address_line_2` VARCHAR(255),
	`delivery_postal_code` VARCHAR(12),
	`delivery_city` VARCHAR(64),
	`delivery_country` VARCHAR(128),
	`delivery_countries_id` INTEGER,
	`delivery_state_province` VARCHAR(64),
	`delivery_company_name` VARCHAR(128),
	`discount` DECIMAL(8,2) DEFAULT 0.00,
	`groups_id` INTEGER DEFAULT 1 NOT NULL,
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL,
	`languages_id` INTEGER NOT NULL,
	`countries_id` INTEGER NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `FI_customers_10` (`groups_id`),
	INDEX `FI_customers_20` (`languages_id`),
	INDEX `FI_customers_30` (`countries_id`),
	INDEX `FI_customers_40` (`billing_countries_id`),
	INDEX `FI_customers_50` (`delivery_countries_id`),
	CONSTRAINT `fk_customers_10`
		FOREIGN KEY (`groups_id`)
		REFERENCES `groups` (`id`)
		ON UPDATE CASCADE,
	CONSTRAINT `fk_customers_20`
		FOREIGN KEY (`languages_id`)
		REFERENCES `languages` (`id`),
	CONSTRAINT `fk_customers_30`
		FOREIGN KEY (`countries_id`)
		REFERENCES `countries` (`id`),
	CONSTRAINT `fk_customers_40`
		FOREIGN KEY (`billing_countries_id`)
		REFERENCES `countries` (`id`),
	CONSTRAINT `fk_customers_50`
		FOREIGN KEY (`delivery_countries_id`)
		REFERENCES `countries` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- domains
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `domains`;

CREATE TABLE `domains`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`domain_name` VARCHAR(255) NOT NULL,
	`domain_key` VARCHAR(12) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `index2` (`domain_key`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- domains_settings
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `domains_settings`;

CREATE TABLE `domains_settings`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`domain_key` VARCHAR(12) NOT NULL,
	`c_key` VARCHAR(128) NOT NULL,
	`ns` VARCHAR(64) NOT NULL,
	`c_value` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `domains_settings_unique` (`c_key`, `ns`),
	INDEX `FI_domains_settings_1` (`domain_key`),
	CONSTRAINT `fk_domains_settings_1`
		FOREIGN KEY (`domain_key`)
		REFERENCES `domains` (`domain_key`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- events
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events`
(
	`id` INTEGER NOT NULL,
	`code` VARCHAR(32) NOT NULL,
	`key` VARCHAR(64) NOT NULL,
	`consultants_id` INTEGER NOT NULL,
	`customers_id` INTEGER NOT NULL,
	`event_date` DATETIME NOT NULL,
	`host` VARCHAR(128) NOT NULL,
	`address_line_1` VARCHAR(128) NOT NULL,
	`address_line_2` VARCHAR(128),
	`postal_code` VARCHAR(12) NOT NULL,
	`city` VARCHAR(64) NOT NULL,
	`phone` VARCHAR(32) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`description` TEXT,
	`type` VARCHAR(3) DEFAULT 'AR' NOT NULL,
	`is_open` TINYINT(1) DEFAULT 0 NOT NULL,
	`notify_hostess` TINYINT(1) DEFAULT 1 NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `key_UNIQUE` (`key`),
	UNIQUE INDEX `code_UNIQUE` (`code`),
	INDEX `FI_events_1` (`consultants_id`),
	INDEX `FI_events_2` (`customers_id`),
	CONSTRAINT `fk_events_1`
		FOREIGN KEY (`consultants_id`)
		REFERENCES `customers` (`id`),
	CONSTRAINT `fk_events_2`
		FOREIGN KEY (`customers_id`)
		REFERENCES `customers` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- events_participants
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `events_participants`;

CREATE TABLE `events_participants`
(
	`id` INTEGER NOT NULL,
	`events_id` INTEGER NOT NULL,
	`key` VARCHAR(64) NOT NULL,
	`invited_by` INTEGER,
	`first_name` VARCHAR(128) NOT NULL,
	`last_name` VARCHAR(128),
	`email` VARCHAR(255),
	`phone` VARCHAR(32),
	`tell_a_friend` TINYINT(1) DEFAULT 0 NOT NULL,
	`notify_by_sms` TINYINT(1) DEFAULT 0 NOT NULL,
	`sms_send_at` DATE,
	`has_accepted` TINYINT(1) DEFAULT 0 NOT NULL,
	`expires_at` DATETIME,
	`responded_at` DATETIME,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `key_UNIQUE` (`key`),
	INDEX `FI_events_participants_1` (`events_id`),
	CONSTRAINT `fk_events_participants_1`
		FOREIGN KEY (`events_id`)
		REFERENCES `events` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- groups
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(45) NOT NULL,
	`discount` DECIMAL(15,4),
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- helpdesk_data_log
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `helpdesk_data_log`;

CREATE TABLE `helpdesk_data_log`
(
	`key` VARCHAR(64) NOT NULL,
	`data` LONGTEXT NOT NULL,
	`created_at` DATETIME NOT NULL,
	PRIMARY KEY (`key`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- languages
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `languages`;

CREATE TABLE `languages`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(32) NOT NULL,
	`local_name` VARCHAR(45) NOT NULL,
	`locale` VARCHAR(12) NOT NULL,
	`iso2` VARCHAR(2) NOT NULL,
	`direction` VARCHAR(3) DEFAULT 'ltr' NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `index2` (`iso2`),
	INDEX `languages_index_3` (`locale`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- mannequin_images
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `mannequin_images`;

CREATE TABLE `mannequin_images`
(
	`master` VARCHAR(128) NOT NULL,
	`color` VARCHAR(32) NOT NULL,
	`layer` INTEGER NOT NULL,
	`image` VARCHAR(128) NOT NULL,
	`icon` VARCHAR(128) NOT NULL,
	`weight` INTEGER DEFAULT 0 NOT NULL,
	`is_main` TINYINT(1) DEFAULT 0 NOT NULL,
	PRIMARY KEY (`master`),
	CONSTRAINT `fk_mannequin_images_1`
		FOREIGN KEY (`master`)
		REFERENCES `products` (`master`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`sku` VARCHAR(128) NOT NULL,
	`master` VARCHAR(128) NOT NULL,
	`size` VARCHAR(32) NOT NULL,
	`color` VARCHAR(128) NOT NULL,
	`unit` VARCHAR(12) NOT NULL,
	`washing` INTEGER,
	`has_video` TINYINT(1) DEFAULT 1 NOT NULL,
	`is_out_of_stock` TINYINT(1) DEFAULT 0 NOT NULL,
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `sku_UNIQUE` (`sku`),
	INDEX `key_size_color` (`size`, `color`),
	INDEX `key_out_of_stock` (`is_out_of_stock`),
	INDEX `index5` (`master`),
	CONSTRAINT `fk_products_1`
		FOREIGN KEY (`sku`)
		REFERENCES `products` (`master`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_domains_prices
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_domains_prices`;

CREATE TABLE `products_domains_prices`
(
	`products_id` INTEGER NOT NULL,
	`domains_id` INTEGER NOT NULL,
	`price` DECIMAL(15,4) NOT NULL,
	`vat` DECIMAL(4,2) NOT NULL,
	`currency_code` VARCHAR(3),
	`from_date` DATETIME NOT NULL,
	`to_date` DATETIME,
	PRIMARY KEY (`products_id`,`domains_id`,`from_date`),
	INDEX `FI_products_domains_prices_2` (`domains_id`),
	CONSTRAINT `fk_products_domains_prices_1`
		FOREIGN KEY (`products_id`)
		REFERENCES `products` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `fk_products_domains_prices_2`
		FOREIGN KEY (`domains_id`)
		REFERENCES `domains` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_images
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_images`;

CREATE TABLE `products_images`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`products_id` INTEGER NOT NULL,
	`image` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `FI_products_images_1` (`products_id`),
	CONSTRAINT `fk_products_images_1`
		FOREIGN KEY (`products_id`)
		REFERENCES `products` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_images_categories_sort
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_images_categories_sort`;

CREATE TABLE `products_images_categories_sort`
(
	`products_id` INTEGER NOT NULL,
	`categories_id` INTEGER NOT NULL,
	`products_images_id` INTEGER,
	`sort` INTEGER,
	PRIMARY KEY (`products_id`,`categories_id`),
	INDEX `FI_products_images_categories_sort_2` (`products_images_id`),
	CONSTRAINT `fk_products_images_categories_sort_1`
		FOREIGN KEY (`products_id`)
		REFERENCES `products` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `fk_products_images_categories_sort_2`
		FOREIGN KEY (`products_images_id`)
		REFERENCES `products_images` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_images_product_references
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_images_product_references`;

CREATE TABLE `products_images_product_references`
(
	`products_images_id` INTEGER NOT NULL,
	`products_id` INTEGER NOT NULL,
	PRIMARY KEY (`products_images_id`,`products_id`),
	INDEX `FI_products_images_product_references_2` (`products_id`),
	CONSTRAINT `fk_products_images_product_references_1`
		FOREIGN KEY (`products_images_id`)
		REFERENCES `products_images` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `fk_products_images_product_references_2`
		FOREIGN KEY (`products_id`)
		REFERENCES `products` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_stock
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_stock`;

CREATE TABLE `products_stock`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`products_id` INTEGER NOT NULL,
	`quantity` INTEGER NOT NULL,
	`available_from` DATE DEFAULT '1970-00-00' NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `key_available_from` (`available_from`),
	INDEX `FI_products_stock_1` (`products_id`),
	CONSTRAINT `fk_products_stock_1`
		FOREIGN KEY (`products_id`)
		REFERENCES `products` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_to_categories
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_to_categories`;

CREATE TABLE `products_to_categories`
(
	`products_id` INTEGER NOT NULL,
	`categories_id` INTEGER NOT NULL,
	PRIMARY KEY (`products_id`,`categories_id`),
	INDEX `FI_ducts_to_categories_ibfk_2` (`categories_id`),
	CONSTRAINT `products_to_categories_ibfk_1`
		FOREIGN KEY (`products_id`)
		REFERENCES `products` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `products_to_categories_ibfk_2`
		FOREIGN KEY (`categories_id`)
		REFERENCES `categories` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_washing_instructions
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_washing_instructions`;

CREATE TABLE `products_washing_instructions`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`code` INTEGER NOT NULL,
	`languages_id` INTEGER NOT NULL,
	`description` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `FI_products_washing_instructions_1` (`languages_id`),
	CONSTRAINT `fk_products_washing_instructions_1`
		FOREIGN KEY (`languages_id`)
		REFERENCES `languages` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- redirects
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `redirects`;

CREATE TABLE `redirects`
(
	`id` INTEGER NOT NULL,
	`source` VARCHAR(255) NOT NULL,
	`target` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `index2` (`source`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- settings
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings`
(
	`c_key` VARCHAR(64) NOT NULL,
	`ns` VARCHAR(64) NOT NULL,
	`title` VARCHAR(128) NOT NULL,
	`c_value` VARCHAR(255) NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`c_key`,`ns`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- zip_to_city
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `zip_to_city`;

CREATE TABLE `zip_to_city`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`zip` VARCHAR(12) NOT NULL,
	`countries_iso2` VARCHAR(2) NOT NULL,
	`city` VARCHAR(128) NOT NULL,
	`county_id` VARCHAR(12),
	`county_name` VARCHAR(128),
	`comment` VARCHAR(255),
	PRIMARY KEY (`id`),
	INDEX `index2` (`zip`, `countries_iso2`),
	INDEX `FI_zip_to_city_1` (`countries_iso2`),
	CONSTRAINT `fk_zip_to_city_1`
		FOREIGN KEY (`countries_iso2`)
		REFERENCES `countries` (`iso2`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`session_id` VARCHAR(32) NOT NULL,
	`payment_gateway_id` INTEGER,
	`state` INTEGER DEFAULT -3 NOT NULL,
	`customers_id` INTEGER,
	`first_name` VARCHAR(128),
	`last_name` VARCHAR(128),
	`email` VARCHAR(255),
	`phone` VARCHAR(32),
	`languages_id` INTEGER NOT NULL,
	`billing_address_line_1` VARCHAR(255),
	`billing_address_line_2` VARCHAR(255),
	`billing_postal_code` VARCHAR(12),
	`billing_city` VARCHAR(64),
	`billing_country` VARCHAR(128),
	`billing_countries_id` INTEGER,
	`billing_state_province` VARCHAR(64),
	`billing_method` VARCHAR(64),
	`delivery_address_line_1` VARCHAR(255),
	`delivery_address_line_2` VARCHAR(255),
	`delivery_postal_code` VARCHAR(12),
	`delivery_city` VARCHAR(64),
	`delivery_country` VARCHAR(128),
	`delivery_countries_id` INTEGER,
	`delivery_state_province` VARCHAR(64),
	`delivery_company_name` VARCHAR(128),
	`delivery_method` VARCHAR(64),
	`finished_at` DATETIME,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `index2` (`session_id`),
	UNIQUE INDEX `index3` (`payment_gateway_id`),
	INDEX `index4` (`customers_id`),
	INDEX `index5` (`languages_id`),
	INDEX `index8` (`state`),
	INDEX `FI_customers_1` (`billing_countries_id`),
	INDEX `FI_customers_2` (`delivery_countries_id`),
	CONSTRAINT `fk_customers_1`
		FOREIGN KEY (`billing_countries_id`)
		REFERENCES `countries` (`id`),
	CONSTRAINT `fk_customers_2`
		FOREIGN KEY (`delivery_countries_id`)
		REFERENCES `countries` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders_attributes
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders_attributes`;

CREATE TABLE `orders_attributes`
(
	`c_key` VARCHAR(64) NOT NULL,
	`ns` VARCHAR(64) NOT NULL,
	`c_value` VARCHAR(255),
	`orders_id` INTEGER NOT NULL,
	PRIMARY KEY (`c_key`,`ns`),
	INDEX `FI_orders_attributes_1` (`orders_id`),
	CONSTRAINT `fk_orders_attributes_1`
		FOREIGN KEY (`orders_id`)
		REFERENCES `orders` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders_lines
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders_lines`;

CREATE TABLE `orders_lines`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`orders_id` INTEGER NOT NULL,
	`type` VARCHAR(12) NOT NULL,
	`tax` DECIMAL(4,2) DEFAULT 0.00,
	`products_id` INTEGER,
	`products_sku` VARCHAR(255),
	`products_name` VARCHAR(255) NOT NULL,
	`products_color` VARCHAR(128),
	`products_size` VARCHAR(32),
	`expected_at` DATE DEFAULT '1970-01-01',
	`price` DECIMAL(15,4),
	`quantity` INTEGER,
	PRIMARY KEY (`id`),
	INDEX `FI_orders_lines_1` (`orders_id`),
	INDEX `FI_orders_lines_2` (`products_id`),
	CONSTRAINT `fk_orders_lines_1`
		FOREIGN KEY (`orders_id`)
		REFERENCES `orders` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `fk_orders_lines_2`
		FOREIGN KEY (`products_id`)
		REFERENCES `products` (`id`)
		ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders_sync_log
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders_sync_log`;

CREATE TABLE `orders_sync_log`
(
	`orders_id` INTEGER NOT NULL,
	`created_at` DATETIME NOT NULL,
	`state` VARCHAR(12) DEFAULT 'ok' NOT NULL,
	`content` TEXT,
	PRIMARY KEY (`orders_id`,`created_at`),
	INDEX `osl_index_1` (`orders_id`, `created_at`),
	CONSTRAINT `fk_orders_lines_3`
		FOREIGN KEY (`orders_id`)
		REFERENCES `orders` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- categories_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `categories_i18n`;

CREATE TABLE `categories_i18n`
(
	`id` INTEGER NOT NULL,
	`locale` VARCHAR(5) DEFAULT 'en_EN' NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`content` TEXT,
	PRIMARY KEY (`id`,`locale`),
	CONSTRAINT `categories_i18n_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `categories` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- cms_thread_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cms_thread_i18n`;

CREATE TABLE `cms_thread_i18n`
(
	`id` INTEGER NOT NULL,
	`locale` VARCHAR(5) DEFAULT 'en_EN' NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`,`locale`),
	CONSTRAINT `cms_thread_i18n_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `cms_thread` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- cms_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cms_i18n`;

CREATE TABLE `cms_i18n`
(
	`id` INTEGER NOT NULL,
	`locale` VARCHAR(5) DEFAULT 'en_EN' NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`path` VARCHAR(255) NOT NULL,
	`content` TEXT,
	`settings` TEXT,
	PRIMARY KEY (`id`,`locale`),
	CONSTRAINT `cms_i18n_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `cms` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_i18n`;

CREATE TABLE `products_i18n`
(
	`id` INTEGER NOT NULL,
	`locale` VARCHAR(5) DEFAULT 'en_EN' NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`content` TEXT,
	PRIMARY KEY (`id`,`locale`),
	CONSTRAINT `products_i18n_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `products` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
