<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1328523785.
 * Generated on 2012-02-06 11:23:05 by enrique
 */
class PropelMigration_1328523785
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

ALTER TABLE `orders` ADD
(
	`billing_first_name` VARCHAR(128) NOT NULL,
	`billing_last_name` VARCHAR(128) NOT NULL,
	`billing_company_name` VARCHAR(128),
	`delivery_first_name` VARCHAR(128) NOT NULL,
	`delivery_last_name` VARCHAR(128) NOT NULL
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

ALTER TABLE `orders` DROP `billing_first_name`;

ALTER TABLE `orders` DROP `billing_last_name`;

ALTER TABLE `orders` DROP `billing_company_name`;

ALTER TABLE `orders` DROP `delivery_first_name`;

ALTER TABLE `orders` DROP `delivery_last_name`;

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}