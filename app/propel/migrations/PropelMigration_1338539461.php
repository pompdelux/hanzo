<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1338539461.
 * Generated on 2012-06-01 10:31:01 by un
 */
class PropelMigration_1338539461
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

CREATE TABLE `wall`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER,
	`customers_id` INTEGER NOT NULL,
	`messate` LONGTEXT NOT NULL,
	`status` TINYINT(1) DEFAULT 1 NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `wall_FI_1` (`parent_id`),
	INDEX `wall_FI_2` (`customers_id`),
	CONSTRAINT `wall_FK_1`
		FOREIGN KEY (`parent_id`)
		REFERENCES `wall` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `wall_FK_2`
		FOREIGN KEY (`customers_id`)
		REFERENCES `customers` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `wall_likes`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`wall_id` INTEGER NOT NULL,
	`customers_id` INTEGER NOT NULL,
	`status` TINYINT(1) DEFAULT 1 NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `wall_likes_FI_1` (`wall_id`),
	INDEX `wall_likes_FI_2` (`customers_id`),
	CONSTRAINT `wall_likes_FK_1`
		FOREIGN KEY (`wall_id`)
		REFERENCES `wall` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `wall_likes_FK_2`
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

DROP TABLE IF EXISTS `wall`;

DROP TABLE IF EXISTS `wall_likes`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
