<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1328013202.
 * Generated on 2012-01-31 13:33:22 by un
 */
class PropelMigration_1328013202
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

ALTER TABLE `orders_attributes` DROP PRIMARY KEY;

DROP INDEX `FI_orders_attributes_1` ON `orders_attributes`;

ALTER TABLE `orders_attributes` ADD PRIMARY KEY (`orders_id`,`ns`,`c_key`);

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

ALTER TABLE `orders_attributes` DROP PRIMARY KEY;

ALTER TABLE `orders_attributes` ADD PRIMARY KEY (`c_key`,`ns`);

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}