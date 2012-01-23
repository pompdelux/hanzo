<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1327319619.
 * Generated on 2012-01-23 12:53:39 by enrique
 */
class PropelMigration_1327319619
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

ALTER TABLE `gothia_accounts` DROP `first_name`;

ALTER TABLE `gothia_accounts` DROP `last_name`;

ALTER TABLE `gothia_accounts` DROP `address`;

ALTER TABLE `gothia_accounts` DROP `postal_code`;

ALTER TABLE `gothia_accounts` DROP `postal_place`;

ALTER TABLE `gothia_accounts` DROP `email`;

ALTER TABLE `gothia_accounts` DROP `phone`;

ALTER TABLE `gothia_accounts` DROP `mobile_phone`;

ALTER TABLE `gothia_accounts` DROP `fax`;

ALTER TABLE `gothia_accounts` DROP `country_code`;

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

ALTER TABLE `gothia_accounts` ADD
(
	`first_name` VARCHAR(128) NOT NULL,
	`last_name` VARCHAR(128) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`postal_code` VARCHAR(12) NOT NULL,
	`postal_place` VARCHAR(64) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`phone` VARCHAR(32) NOT NULL,
	`mobile_phone` VARCHAR(32),
	`fax` VARCHAR(32),
	`country_code` VARCHAR(4)
);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}