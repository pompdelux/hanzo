<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1329316298.
 * Generated on 2012-02-15 15:31:38 by un
 */
class PropelMigration_1329316298
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

DROP INDEX `FI_orders_attributes_1` ON `orders_attributes`;

CREATE TABLE `shipping_methods`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`carrier` VARCHAR(255) NOT NULL,
	`external_id` VARCHAR(32) NOT NULL,
	`calc_engine` VARCHAR(32) DEFAULT \'flat\' NOT NULL,
	`price` DECIMAL(15,4) NOT NULL,
	`fee` DECIMAL(15,4) DEFAULT 0.00,
	`fee_external_id` VARCHAR(32),
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL,
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

DROP TABLE IF EXISTS `shipping_methods`;

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
