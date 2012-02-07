<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1328618997.
 * Generated on 2012-02-07 13:49:57 by un
 */
class PropelMigration_1328618997
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

DROP TABLE IF EXISTS `consultants_info`;

ALTER TABLE `customers` DROP `initials`;

ALTER TABLE `customers` ADD CONSTRAINT `customers_FK_2`
	FOREIGN KEY (`id`)
	REFERENCES `consultants` (`id`)
	ON DELETE CASCADE;

DROP INDEX `FI_orders_attributes_1` ON `orders_attributes`;

CREATE TABLE `consultants`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`initials` VARCHAR(6),
	`info` TEXT,
	`event_notes` TEXT,
	PRIMARY KEY (`id`)
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

DROP TABLE IF EXISTS `consultants`;

ALTER TABLE `customers` DROP FOREIGN KEY `customers_FK_2`;

ALTER TABLE `customers` ADD
(
	`initials` VARCHAR(6)
);

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

CREATE TABLE `consultants_info`
(
	`consultants_id` INTEGER NOT NULL,
	`description` TEXT,
	`max_notified` TINYINT(1) DEFAULT 0 NOT NULL,
	PRIMARY KEY (`consultants_id`),
	CONSTRAINT `fk_consultants_info_1`
		FOREIGN KEY (`consultants_id`)
		REFERENCES `customers` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}