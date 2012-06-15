<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1339758669.
 * Generated on 2012-06-15 13:11:09 by un
 */
class PropelMigration_1339758669
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

CREATE TABLE `customers_gotia_attributes`
(
	`customers_id` INTEGER NOT NULL,
	`c_key` VARCHAR(255) NOT NULL,
	`c_value` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`customers_id`,`c_key`),
	CONSTRAINT `customers_gotia_attributes_FK_1`
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

DROP TABLE IF EXISTS `customers_gotia_attributes`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
