<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1326963085.
 * Generated on 2012-01-19 09:51:25 by un
 */
class PropelMigration_1326963085
{

	public function preUp($manager)
	{
		// add the pre-migration code here
	}

	public function postUp($manager)
	{
		// add the post-migration code here
	}

	public function preDown($manager)
	{
		// add the pre-migration code here
	}

	public function postDown($manager)
	{
		// add the post-migration code here
	}

	/**
	 * Get the SQL statements for the Up migration
	 *
	 * @return array list of the SQL strings to execute for the Up migration
	 *               the keys being the datasources
	 */
	public function getUpSQL()
	{
		return array (
  'default' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP INDEX `index2` ON `consultants_info`;

DROP INDEX `index3` ON `consultants_info`;

ALTER TABLE `consultants_info` DROP `latitude`;

ALTER TABLE `consultants_info` DROP `longitude`;

ALTER TABLE `customers` DROP FOREIGN KEY `fk_customers_30`;

ALTER TABLE `customers` DROP FOREIGN KEY `fk_customers_40`;

ALTER TABLE `customers` DROP FOREIGN KEY `fk_customers_50`;

DROP INDEX `FI_customers_30` ON `customers`;

DROP INDEX `FI_customers_40` ON `customers`;

DROP INDEX `FI_customers_50` ON `customers`;

ALTER TABLE `customers` DROP `billing_address_line_1`;

ALTER TABLE `customers` DROP `billing_address_line_2`;

ALTER TABLE `customers` DROP `billing_postal_code`;

ALTER TABLE `customers` DROP `billing_city`;

ALTER TABLE `customers` DROP `billing_country`;

ALTER TABLE `customers` DROP `billing_countries_id`;

ALTER TABLE `customers` DROP `billing_state_province`;

ALTER TABLE `customers` DROP `delivery_address_line_1`;

ALTER TABLE `customers` DROP `delivery_address_line_2`;

ALTER TABLE `customers` DROP `delivery_postal_code`;

ALTER TABLE `customers` DROP `delivery_city`;

ALTER TABLE `customers` DROP `delivery_country`;

ALTER TABLE `customers` DROP `delivery_countries_id`;

ALTER TABLE `customers` DROP `delivery_state_province`;

ALTER TABLE `customers` DROP `delivery_company_name`;

ALTER TABLE `customers` DROP `countries_id`;

CREATE TABLE `addresses`
(
	`customers_id` INTEGER NOT NULL,
	`type` VARCHAR(10) DEFAULT \'payment\' NOT NULL,
	`address_line_1` VARCHAR(255),
	`address_line_2` VARCHAR(255),
	`postal_code` VARCHAR(12),
	`city` VARCHAR(64),
	`country` VARCHAR(128),
	`countries_id` INTEGER,
	`state_province` VARCHAR(64),
	`company_name` VARCHAR(128),
	`latitude` DOUBLE,
	`longitude` DOUBLE,
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

CREATE TABLE `gothia_accounts`
(
	`customers_id` INTEGER NOT NULL,
	`first_name` VARCHAR(128) NOT NULL,
	`last_name` VARCHAR(128) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`postal_code` VARCHAR(12) NOT NULL,
	`postal_place` VARCHAR(64) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`phone` VARCHAR(32) NOT NULL,
	`mobile_phone` VARCHAR(32),
	`fax` VARCHAR(32),
	`country_code` VARCHAR(4),
	`distribution_by` VARCHAR(255),
	`distribution_type` VARCHAR(255),
	`social_security_num` VARCHAR(12) NOT NULL,
	PRIMARY KEY (`customers_id`),
	CONSTRAINT `fk_gothia_account_to_customer`
		FOREIGN KEY (`customers_id`)
		REFERENCES `customers` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

	/**
	 * Get the SQL statements for the Down migration
	 *
	 * @return array list of the SQL strings to execute for the Down migration
	 *               the keys being the datasources
	 */
	public function getDownSQL()
	{
		return array (
  'default' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `addresses`;

DROP TABLE IF EXISTS `gothia_accounts`;

ALTER TABLE `consultants_info` ADD
(
	`latitude` FLOAT(10,6),
	`longitude` FLOAT(10,6)
);

CREATE INDEX `index2` ON `consultants_info` (`consultants_id`);

CREATE INDEX `index3` ON `consultants_info` (`latitude`,`longitude`);

ALTER TABLE `customers` ADD
(
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
	`countries_id` INTEGER NOT NULL
);

CREATE INDEX `FI_customers_30` ON `customers` (`countries_id`);

CREATE INDEX `FI_customers_40` ON `customers` (`billing_countries_id`);

CREATE INDEX `FI_customers_50` ON `customers` (`delivery_countries_id`);

ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_30`
	FOREIGN KEY (`countries_id`)
	REFERENCES `countries` (`id`);

ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_40`
	FOREIGN KEY (`billing_countries_id`)
	REFERENCES `countries` (`id`);

ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_50`
	FOREIGN KEY (`delivery_countries_id`)
	REFERENCES `countries` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}