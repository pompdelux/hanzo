<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1321970003.
 * Generated on 2011-11-22 14:53:23 by un
 */
class PropelMigration_1321970003
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

CREATE TABLE `orders_sync_log`
(
	`orders_id` INTEGER NOT NULL,
	`created_at` DATETIME NOT NULL,
	`state` VARCHAR(12) DEFAULT "ok" NOT NULL,
	`content` TEXT,
	PRIMARY KEY (`orders_id`,`created_at`),
	INDEX `osl_index_1` (`orders_id`, `created_at`),
	CONSTRAINT `fk_orders_lines_1`
		FOREIGN KEY (`orders_id`)
		REFERENCES `orders` (`id`)
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

DROP TABLE IF EXISTS `orders_sync_log`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
