<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1346072017.
 * Generated on 2012-08-27 14:53:37 
 */
class PropelMigration_1346072017
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

CREATE INDEX `cms_I_1` ON `cms` (`path`);

CREATE INDEX `cms_I_2` ON `cms` (`old_path`);

ALTER TABLE `gothia_accounts` DROP `external_id`;

ALTER TABLE `products_quantity_discount` ADD CONSTRAINT `products_quantity_discount_FK_1`
	FOREIGN KEY (`products_master`)
	REFERENCES `products` (`sku`)
	ON DELETE CASCADE;

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

DROP INDEX `cms_I_1` ON `cms`;

DROP INDEX `cms_I_2` ON `cms`;

ALTER TABLE `gothia_accounts` ADD
(
	`external_id` INTEGER NOT NULL
);

ALTER TABLE `products_quantity_discount` DROP FOREIGN KEY `products_quantity_discount_FK_1`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}