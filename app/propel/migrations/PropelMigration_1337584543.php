<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1337584543.
 * Generated on 2012-05-21 09:15:43 by un
 */
class PropelMigration_1337584543
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

DROP TABLE IF EXISTS `related_products`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
