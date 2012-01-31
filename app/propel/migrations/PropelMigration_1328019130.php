<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1328019130.
 * Generated on 2012-01-31 15:12:10 by un
 */
class PropelMigration_1328019130
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
	`in_edit` TINYINT(1) DEFAULT 0 NOT NULL
);

DROP INDEX `FI_orders_attributes_1` ON `orders_attributes`;

CREATE TABLE `orders_state_log`
(
	`orders_id` INTEGER NOT NULL,
	`state` INTEGER NOT NULL,
	`created_at` DATETIME NOT NULL,
	`message` VARCHAR(128) NOT NULL,
	PRIMARY KEY (`orders_id`,`state`,`created_at`),
	CONSTRAINT `orders_state_log_FK_1`
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

DROP TABLE IF EXISTS `orders_state_log`;

ALTER TABLE `orders` DROP `in_edit`;

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}