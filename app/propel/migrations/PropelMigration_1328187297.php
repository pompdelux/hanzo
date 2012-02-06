<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1328187297.
 * Generated on 2012-02-02 13:54:57 by enrique
 */
class PropelMigration_1328187297
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

ALTER TABLE `addresses` CHANGE `address_line_1` `address_line_1` VARCHAR(255) NOT NULL;

ALTER TABLE `addresses` CHANGE `postal_code` `postal_code` VARCHAR(12) NOT NULL;

ALTER TABLE `addresses` CHANGE `city` `city` VARCHAR(64) NOT NULL;

ALTER TABLE `addresses` CHANGE `country` `country` VARCHAR(128) NOT NULL;

ALTER TABLE `addresses` CHANGE `countries_id` `countries_id` INTEGER NOT NULL;

ALTER TABLE `addresses` ADD
(
	`first_name` VARCHAR(128) NOT NULL,
	`last_name` VARCHAR(128) NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME
);

DROP INDEX `FI_orders_attributes_1` ON `orders_attributes`;

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

ALTER TABLE `addresses` CHANGE `address_line_1` `address_line_1` VARCHAR(255);

ALTER TABLE `addresses` CHANGE `postal_code` `postal_code` VARCHAR(12);

ALTER TABLE `addresses` CHANGE `city` `city` VARCHAR(64);

ALTER TABLE `addresses` CHANGE `country` `country` VARCHAR(128);

ALTER TABLE `addresses` CHANGE `countries_id` `countries_id` INTEGER;

ALTER TABLE `addresses` DROP `first_name`;

ALTER TABLE `addresses` DROP `last_name`;

ALTER TABLE `addresses` DROP `created_at`;

ALTER TABLE `addresses` DROP `updated_at`;

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}