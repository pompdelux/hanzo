<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1329398440.
 * Generated on 2012-02-16 14:20:40 by un
 */
class PropelMigration_1329398440
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

ALTER TABLE `products` CHANGE `sku` `sku` VARCHAR(128) NOT NULL;

ALTER TABLE `products` CHANGE `master` `master` VARCHAR(128);

ALTER TABLE `products` CHANGE `size` `size` VARCHAR(32);

ALTER TABLE `products` CHANGE `color` `color` VARCHAR(128);

ALTER TABLE `products` CHANGE `unit` `unit` VARCHAR(12);

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

ALTER TABLE `products` CHANGE `sku` `sku` VARCHAR(128);

ALTER TABLE `products` CHANGE `master` `master` VARCHAR(128) NOT NULL;

ALTER TABLE `products` CHANGE `size` `size` VARCHAR(32) NOT NULL;

ALTER TABLE `products` CHANGE `color` `color` VARCHAR(128) NOT NULL;

ALTER TABLE `products` CHANGE `unit` `unit` VARCHAR(12) NOT NULL;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
