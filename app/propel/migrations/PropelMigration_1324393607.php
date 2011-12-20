<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1324393607.
 * Generated on 2011-12-20 16:06:47 by un
 */
class PropelMigration_1324393607
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

ALTER TABLE `products` ADD CONSTRAINT `products_FK_2`
	FOREIGN KEY (`washing`)
	REFERENCES `products_washing_instructions` (`code`);

ALTER TABLE `products_stock` CHANGE `available_from` `available_from` DATETIME DEFAULT \'2000-01-01 00:00:00\' NOT NULL;

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

ALTER TABLE `products` DROP FOREIGN KEY `products_FK_2`;

ALTER TABLE `products_stock` CHANGE `available_from` `available_from` DATE DEFAULT \'1970-00-00\' NOT NULL;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}