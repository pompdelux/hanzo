
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
    `updated_by` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `FI_cms_1` (`cms_thread_id`),
    INDEX `FI_cms_2` (`parent_id`),
    CONSTRAINT `fk_cms_1`
        FOREIGN KEY (`cms_thread_id`)
        REFERENCES `cms_thread` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_cms_2`
        FOREIGN KEY (`parent_id`)
        REFERENCES `cms` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- cms_revision
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cms_revision`;

CREATE TABLE `cms_revision`
(
    `id` INTEGER NOT NULL,
    `created_at` DATETIME NOT NULL,
    `publish_on_date` DATETIME,
    `revision` TEXT NOT NULL,
    PRIMARY KEY (`id`,`created_at`)
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
    `currency_code` VARCHAR(3) NOT NULL,
    `currency_name` VARCHAR(32) NOT NULL,
    `vat` DECIMAL(4,2),
    `calling_code` INTEGER,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `index2` (`code`),
    UNIQUE INDEX `index3` (`iso2`),
    UNIQUE INDEX `index4` (`iso3`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- gift_cards
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gift_cards`;

CREATE TABLE `gift_cards`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(12) NOT NULL,
    `amount` DECIMAL(15,4) NOT NULL,
    `currency_code` VARCHAR(3) NOT NULL,
    `active_from` DATETIME,
    `active_to` DATETIME,
    `is_active` TINYINT(1) DEFAULT 1 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `code_UNIQUE` (`code`),
    INDEX `index3` (`active_from`, `active_to`),
    INDEX `index4` (`is_active`),
    INDEX `index5` (`currency_code`)
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
    `min_purchase_amount` DECIMAL(15,4),
    `currency_code` VARCHAR(3) NOT NULL,
    `active_from` DATETIME,
    `active_to` DATETIME,
    `is_active` TINYINT(1) DEFAULT 1 NOT NULL,
    `is_used` TINYINT(1) DEFAULT 0 NOT NULL,
    `is_reusable` TINYINT(1) DEFAULT 0 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `code_UNIQUE` (`code`),
    INDEX `index3` (`active_from`, `active_to`),
    INDEX `index4` (`is_active`),
    INDEX `index5` (`is_used`),
    INDEX `index7` (`is_reusable`),
    INDEX `index6` (`currency_code`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders_to_coupons
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders_to_coupons`;

CREATE TABLE `orders_to_coupons`
(
    `orders_id` INTEGER NOT NULL,
    `coupons_id` INTEGER NOT NULL,
    `amount` DECIMAL(15,4) NOT NULL,
    PRIMARY KEY (`orders_id`,`coupons_id`),
    INDEX `orders_to_coupons_FI_1` (`coupons_id`),
    CONSTRAINT `orders_to_coupons_FK_1`
        FOREIGN KEY (`coupons_id`)
        REFERENCES `coupons` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `orders_to_coupons_FK_2`
        FOREIGN KEY (`orders_id`)
        REFERENCES `orders` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customers
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `groups_id` INTEGER DEFAULT 1 NOT NULL,
    `title` VARCHAR(12),
    `first_name` VARCHAR(128) NOT NULL,
    `last_name` VARCHAR(128) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(32),
    `password` VARCHAR(128) NOT NULL,
    `password_clear` VARCHAR(45),
    `discount` DECIMAL(8,2) DEFAULT 0.00,
    `is_active` TINYINT(1) DEFAULT 1 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `customers_email` (`email`),
    INDEX `customers_FI_1` (`groups_id`),
    CONSTRAINT `customers_FK_1`
        FOREIGN KEY (`groups_id`)
        REFERENCES `groups` (`id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- consultants
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `consultants`;

CREATE TABLE `consultants`
(
    `initials` VARCHAR(10),
    `info` TEXT,
    `event_notes` TEXT,
    `hide_info` TINYINT(1) DEFAULT 0 NOT NULL,
    `max_notified` TINYINT(1) DEFAULT 0 NOT NULL,
    `id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `consultants_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `customers` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- addresses
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `addresses`;

CREATE TABLE `addresses`
(
    `customers_id` INTEGER NOT NULL,
    `type` VARCHAR(32) DEFAULT 'payment' NOT NULL,
    `title` VARCHAR(12),
    `first_name` VARCHAR(128) NOT NULL,
    `last_name` VARCHAR(128) NOT NULL,
    `address_line_1` VARCHAR(255) NOT NULL,
    `address_line_2` VARCHAR(255),
    `postal_code` VARCHAR(12) NOT NULL,
    `city` VARCHAR(64) NOT NULL,
    `country` VARCHAR(128) NOT NULL,
    `countries_id` INTEGER NOT NULL,
    `state_province` VARCHAR(64),
    `company_name` VARCHAR(128),
    `external_address_id` VARCHAR(128),
    `latitude` DOUBLE,
    `longitude` DOUBLE,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`customers_id`,`type`),
    INDEX `addresses_FI_2` (`countries_id`),
    CONSTRAINT `addresses_FK_1`
        FOREIGN KEY (`customers_id`)
        REFERENCES `customers` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `addresses_FK_2`
        FOREIGN KEY (`countries_id`)
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
    UNIQUE INDEX `domains_settings_unique` (`c_key`, `ns`, `domain_key`),
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
    `id` INTEGER NOT NULL AUTO_INCREMENT,
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
    `is_open` TINYINT(1),
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
    `id` INTEGER NOT NULL AUTO_INCREMENT,
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
    `locale` VARCHAR(5) NOT NULL,
    `iso2` VARCHAR(2) NOT NULL,
    `direction` VARCHAR(3) DEFAULT 'ltr' NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `languages_index_3` (`locale`),
    INDEX `index2` (`iso2`)
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
    PRIMARY KEY (`master`,`color`),
    CONSTRAINT `fk_mannequin_images_1`
        FOREIGN KEY (`master`)
        REFERENCES `products` (`sku`)
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
    `locale` VARCHAR(5) NOT NULL,
    `description` TEXT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `key_code` (`code`),
    INDEX `FI_products_washing_instructions_1` (`locale`),
    CONSTRAINT `fk_products_washing_instructions_1`
        FOREIGN KEY (`locale`)
        REFERENCES `languages` (`locale`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `sku` VARCHAR(128) NOT NULL,
    `master` VARCHAR(128),
    `size` VARCHAR(32),
    `color` VARCHAR(128),
    `unit` VARCHAR(12),
    `washing` INTEGER,
    `has_video` TINYINT(1) DEFAULT 1 NOT NULL,
    `is_out_of_stock` TINYINT(1) DEFAULT 0 NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1 NOT NULL,
    `is_voucher` TINYINT(1) DEFAULT 0 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `sku_UNIQUE` (`sku`),
    INDEX `key_size_color` (`size`, `color`),
    INDEX `key_out_of_stock` (`is_out_of_stock`),
    INDEX `key_is_voucher` (`is_voucher`),
    INDEX `index5` (`master`),
    INDEX `products_FI_2` (`washing`),
    CONSTRAINT `products_FK_1`
        FOREIGN KEY (`master`)
        REFERENCES `products` (`sku`)
        ON DELETE CASCADE,
    CONSTRAINT `products_FK_2`
        FOREIGN KEY (`washing`)
        REFERENCES `products_washing_instructions` (`code`)
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
    `vat` DECIMAL(15,4) NOT NULL,
    `currency_id` INTEGER NOT NULL,
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
    `color` VARCHAR(128),
    `type` VARCHAR(128),
    PRIMARY KEY (`id`),
    INDEX `FI_products_images_1` (`products_id`),
    CONSTRAINT `fk_products_images_1`
        FOREIGN KEY (`products_id`)
        REFERENCES `products` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products_images_categories_sort
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_images_categories_sort`;

CREATE TABLE `products_images_categories_sort`
(
    `products_id` INTEGER NOT NULL,
    `categories_id` INTEGER NOT NULL,
    `products_images_id` INTEGER NOT NULL,
    `sort` INTEGER,
    PRIMARY KEY (`products_id`,`categories_id`,`products_images_id`),
    INDEX `FI_products_images_categories_sort_2` (`products_images_id`),
    INDEX `FI_products_images_categories_sort_3` (`categories_id`),
    CONSTRAINT `fk_products_images_categories_sort_1`
        FOREIGN KEY (`products_id`)
        REFERENCES `products` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_products_images_categories_sort_2`
        FOREIGN KEY (`products_images_id`)
        REFERENCES `products_images` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_products_images_categories_sort_3`
        FOREIGN KEY (`categories_id`)
        REFERENCES `categories` (`id`)
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
    `color` VARCHAR(255),
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
-- products_quantity_discount
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `products_quantity_discount`;

CREATE TABLE `products_quantity_discount`
(
    `products_master` VARCHAR(128) NOT NULL,
    `domains_id` INTEGER NOT NULL,
    `span` INTEGER NOT NULL,
    `discount` DECIMAL(15,4) NOT NULL,
    PRIMARY KEY (`products_master`,`domains_id`,`span`),
    INDEX `products_quantity_discount_FI_2` (`domains_id`),
    CONSTRAINT `products_quantity_discount_FK_1`
        FOREIGN KEY (`products_master`)
        REFERENCES `products` (`sku`)
        ON DELETE CASCADE,
    CONSTRAINT `products_quantity_discount_FK_2`
        FOREIGN KEY (`domains_id`)
        REFERENCES `domains` (`id`)
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
    `available_from` DATE DEFAULT '2000-01-01' NOT NULL,
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
-- redirects
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `redirects`;

CREATE TABLE `redirects`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `source` VARCHAR(255) NOT NULL,
    `target` VARCHAR(255) NOT NULL,
    `domain_key` VARCHAR(12) NOT NULL,
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
    `c_value` TEXT NOT NULL,
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
    `version_id` INTEGER DEFAULT 1 NOT NULL,
    `session_id` VARCHAR(32) NOT NULL,
    `payment_gateway_id` INTEGER,
    `state` INTEGER DEFAULT -50 NOT NULL,
    `in_edit` TINYINT(1) DEFAULT 0 NOT NULL,
    `customers_id` INTEGER,
    `first_name` VARCHAR(128),
    `last_name` VARCHAR(128),
    `email` VARCHAR(255),
    `phone` VARCHAR(32),
    `languages_id` INTEGER NOT NULL,
    `currency_code` VARCHAR(12) DEFAULT '' NOT NULL,
    `billing_title` VARCHAR(12),
    `billing_first_name` VARCHAR(128) NOT NULL,
    `billing_last_name` VARCHAR(128) NOT NULL,
    `billing_address_line_1` VARCHAR(255),
    `billing_address_line_2` VARCHAR(255),
    `billing_postal_code` VARCHAR(12),
    `billing_city` VARCHAR(64),
    `billing_country` VARCHAR(128),
    `billing_countries_id` INTEGER,
    `billing_state_province` VARCHAR(64),
    `billing_company_name` VARCHAR(128),
    `billing_method` VARCHAR(64),
    `billing_external_address_id` VARCHAR(128),
    `delivery_title` VARCHAR(12),
    `delivery_first_name` VARCHAR(128) NOT NULL,
    `delivery_last_name` VARCHAR(128) NOT NULL,
    `delivery_address_line_1` VARCHAR(255),
    `delivery_address_line_2` VARCHAR(255),
    `delivery_postal_code` VARCHAR(12),
    `delivery_city` VARCHAR(64),
    `delivery_country` VARCHAR(128),
    `delivery_countries_id` INTEGER,
    `delivery_state_province` VARCHAR(64),
    `delivery_company_name` VARCHAR(128),
    `delivery_method` VARCHAR(64),
    `delivery_external_address_id` VARCHAR(128),
    `events_id` INTEGER,
    `finished_at` DATETIME,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `index2` (`session_id`),
    UNIQUE INDEX `index3` (`payment_gateway_id`),
    INDEX `index5` (`languages_id`),
    INDEX `index8` (`state`),
    INDEX `orders_FI_1` (`customers_id`),
    INDEX `FI_customers_1` (`billing_countries_id`),
    INDEX `FI_customers_2` (`delivery_countries_id`),
    INDEX `orders_FI_4` (`events_id`),
    CONSTRAINT `orders_FK_1`
        FOREIGN KEY (`customers_id`)
        REFERENCES `customers` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT `fk_customers_1`
        FOREIGN KEY (`billing_countries_id`)
        REFERENCES `countries` (`id`),
    CONSTRAINT `fk_customers_2`
        FOREIGN KEY (`delivery_countries_id`)
        REFERENCES `countries` (`id`),
    CONSTRAINT `orders_FK_4`
        FOREIGN KEY (`events_id`)
        REFERENCES `events` (`id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders_attributes
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders_attributes`;

CREATE TABLE `orders_attributes`
(
    `orders_id` INTEGER NOT NULL,
    `ns` VARCHAR(64) NOT NULL,
    `c_key` VARCHAR(64) NOT NULL,
    `c_value` VARCHAR(255),
    PRIMARY KEY (`orders_id`,`ns`,`c_key`),
    CONSTRAINT `orders_attributes_FK_1`
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
    `products_id` INTEGER,
    `products_sku` VARCHAR(255),
    `products_name` VARCHAR(255) NOT NULL,
    `products_color` VARCHAR(128),
    `products_size` VARCHAR(32) NOT NULL,
    `expected_at` DATE DEFAULT '1970-01-01',
    `original_price` DECIMAL(15,4),
    `price` DECIMAL(15,4),
    `vat` DECIMAL(15,4) DEFAULT 0.00,
    `quantity` INTEGER,
    `unit` VARCHAR(12),
    `is_voucher` TINYINT(1) DEFAULT 0 NOT NULL,
    `note` VARCHAR(255),
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
-- orders_state_log
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders_state_log`;

CREATE TABLE `orders_state_log`
(
    `orders_id` INTEGER NOT NULL,
    `state` INTEGER NOT NULL,
    `created_at` DATETIME NOT NULL,
    `message` VARCHAR(128) NOT NULL,
    PRIMARY KEY (`orders_id`,`state`,`created_at`),
    CONSTRAINT `orders_state_log_FK_1`
        FOREIGN KEY (`orders_id`)
        REFERENCES `orders` (`id`)
        ON DELETE CASCADE
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
    `comment` TEXT,
    PRIMARY KEY (`orders_id`,`created_at`),
    CONSTRAINT `fk_orders_lines_3`
        FOREIGN KEY (`orders_id`)
        REFERENCES `orders` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders_versions
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders_versions`;

CREATE TABLE `orders_versions`
(
    `orders_id` INTEGER NOT NULL,
    `version_id` INTEGER NOT NULL,
    `created_at` DATETIME NOT NULL,
    `content` LONGTEXT NOT NULL,
    PRIMARY KEY (`orders_id`,`version_id`),
    CONSTRAINT `orders_versions_FK_1`
        FOREIGN KEY (`orders_id`)
        REFERENCES `orders` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders_deleted_log
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `orders_deleted_log`;

CREATE TABLE `orders_deleted_log`
(
    `orders_id` INTEGER NOT NULL,
    `customers_id` INTEGER,
    `name` VARCHAR(255),
    `email` VARCHAR(255),
    `trigger` VARCHAR(255),
    `content` LONGTEXT NOT NULL,
    `deleted_by` VARCHAR(255) NOT NULL,
    `deleted_at` DATETIME NOT NULL,
    PRIMARY KEY (`orders_id`),
    INDEX `orders_deleted_log_I_1` (`customers_id`),
    INDEX `orders_deleted_log_I_2` (`email`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- sequences
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `sequences`;

CREATE TABLE `sequences`
(
    `name` VARCHAR(32) NOT NULL,
    `id` BIGINT NOT NULL,
    PRIMARY KEY (`name`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- related_products
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `related_products`;

CREATE TABLE `related_products`
(
    `master` VARCHAR(128) NOT NULL,
    `sku` VARCHAR(128) NOT NULL,
    PRIMARY KEY (`master`,`sku`),
    INDEX `related_products_FI_2` (`sku`),
    CONSTRAINT `related_products_FK_1`
        FOREIGN KEY (`master`)
        REFERENCES `products` (`sku`)
        ON DELETE CASCADE,
    CONSTRAINT `related_products_FK_2`
        FOREIGN KEY (`sku`)
        REFERENCES `products` (`sku`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- wall
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `wall`;

CREATE TABLE `wall`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `parent_id` INTEGER,
    `customers_id` INTEGER NOT NULL,
    `messate` LONGTEXT NOT NULL,
    `status` TINYINT(1) DEFAULT 1 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `wall_FI_1` (`parent_id`),
    INDEX `wall_FI_2` (`customers_id`),
    CONSTRAINT `wall_FK_1`
        FOREIGN KEY (`parent_id`)
        REFERENCES `wall` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `wall_FK_2`
        FOREIGN KEY (`customers_id`)
        REFERENCES `customers` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- wall_likes
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `wall_likes`;

CREATE TABLE `wall_likes`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `wall_id` INTEGER NOT NULL,
    `customers_id` INTEGER NOT NULL,
    `status` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `wall_likes_FI_1` (`wall_id`),
    INDEX `wall_likes_FI_2` (`customers_id`),
    CONSTRAINT `wall_likes_FK_1`
        FOREIGN KEY (`wall_id`)
        REFERENCES `wall` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `wall_likes_FK_2`
        FOREIGN KEY (`customers_id`)
        REFERENCES `customers` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- consultant_newsletter_drafts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `consultant_newsletter_drafts`;

CREATE TABLE `consultant_newsletter_drafts`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `consultants_id` INTEGER NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `content` TEXT,
    PRIMARY KEY (`id`),
    INDEX `consultant_newsletter_drafts_FI_1` (`consultants_id`),
    CONSTRAINT `consultant_newsletter_drafts_FK_1`
        FOREIGN KEY (`consultants_id`)
        REFERENCES `customers` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- gothia_accounts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gothia_accounts`;

CREATE TABLE `gothia_accounts`
(
    `customers_id` INTEGER NOT NULL,
    `distribution_by` VARCHAR(255),
    `distribution_type` VARCHAR(255),
    `social_security_num` VARCHAR(12) NOT NULL,
    PRIMARY KEY (`customers_id`),
    CONSTRAINT `fk_gothia_account_to_customer`
        FOREIGN KEY (`customers_id`)
        REFERENCES `customers` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- search_products_tags
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `search_products_tags`;

CREATE TABLE `search_products_tags`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `master_products_id` INTEGER NOT NULL,
    `products_id` INTEGER NOT NULL,
    `token` VARCHAR(128) NOT NULL,
    `locale` VARCHAR(12) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `index_token_locale` (`token`, `locale`),
    INDEX `FI_spt_products_images_1` (`master_products_id`),
    INDEX `FI_spt_products_images_2` (`products_id`),
    CONSTRAINT `fk_spt_products_images_1`
        FOREIGN KEY (`master_products_id`)
        REFERENCES `products` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_spt_products_images_2`
        FOREIGN KEY (`products_id`)
        REFERENCES `products` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- messages
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `ns` VARCHAR(12) NOT NULL,
    `key` VARCHAR(128) NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `key_UNIQUE` (`ns`, `key`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- shipping_methods
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_methods`;

CREATE TABLE `shipping_methods`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `carrier` VARCHAR(255) NOT NULL,
    `method` VARCHAR(255) NOT NULL,
    `external_id` VARCHAR(32) NOT NULL,
    `calc_engine` VARCHAR(32) DEFAULT 'flat' NOT NULL,
    `price` DECIMAL(15,4) NOT NULL,
    `fee` DECIMAL(15,4) DEFAULT 0.00,
    `fee_external_id` VARCHAR(32),
    `is_active` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- free_shipping
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `free_shipping`;

CREATE TABLE `free_shipping`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `domain_key` VARCHAR(12) NOT NULL,
    `break_at` DECIMAL(15,4) NOT NULL,
    `valid_from` DATE,
    `valid_to` DATE,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `domain_key_index` (`domain_key`),
    INDEX `date_index` (`valid_from`, `valid_to`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- categories_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `categories_i18n`;

CREATE TABLE `categories_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'da_DK' NOT NULL,
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
    `locale` VARCHAR(5) DEFAULT 'da_DK' NOT NULL,
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
    `locale` VARCHAR(5) DEFAULT 'da_DK' NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `path` VARCHAR(255) NOT NULL,
    `old_path` VARCHAR(255),
    `content` TEXT,
    `settings` TEXT,
    `is_restricted` TINYINT(1) DEFAULT 0 NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1 NOT NULL,
    `on_mobile` TINYINT(1) DEFAULT 1 NOT NULL,
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
    `locale` VARCHAR(5) DEFAULT 'da_DK' NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `products_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `products` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- messages_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `messages_i18n`;

CREATE TABLE `messages_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'da_DK' NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `body` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `messages_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `messages` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
