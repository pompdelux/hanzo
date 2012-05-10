<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1336680673.
 * Generated on 2012-05-10 22:11:13 by un
 */
class PropelMigration_1336680673
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

CREATE TABLE `products_quantity_discount`
(
	`products_master` VARCHAR(128) NOT NULL,
	`domains_id` INTEGER NOT NULL,
	`span` INTEGER NOT NULL,
	`discount` DECIMAL(15,4) NOT NULL,
	PRIMARY KEY (`products_master`,`domains_id`),
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

DROP TABLE IF EXISTS `products_quantity_discount`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
