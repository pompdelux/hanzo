<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1347534758.
 * Generated on 2012-09-13 13:12:38 by un
 */
class PropelMigration_1347534758
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

CREATE TABLE `orders_deleted_log`
(
	`orders_id` INTEGER NOT NULL,
	`customers_id` INTEGER,
	`name` VARCHAR(255),
	`email` VARCHAR(255),
	`trigger` VARCHAR(255),
	`content` LONGTEXT NOT NULL,
	`deleted_by` VARCHAR(255) NOT NULL,
	`deleted_at` DATETIME NOT NULL,
	PRIMARY KEY (`orders_id`)
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

DROP TABLE IF EXISTS `orders_deleted_log`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
