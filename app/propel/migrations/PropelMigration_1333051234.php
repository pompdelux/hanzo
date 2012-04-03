<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1333051234.
 * Generated on 2012-03-29 22:00:34 by un
 */
class PropelMigration_1333051234
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
	`version_id` INTEGER DEFAULT 1 NOT NULL
);

CREATE TABLE `orders_versions`
(
	`orders_id` INTEGER NOT NULL,
	`version_id` INTEGER NOT NULL,
	`created_at` DATETIME NOT NULL,
	`content` LONGTEXT NOT NULL,
	PRIMARY KEY (`orders_id`,`version_id`,`created_at`),
	CONSTRAINT `orders_versions_FK_1`
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

DROP TABLE IF EXISTS `orders_versions`;

ALTER TABLE `orders` DROP `version_id`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
