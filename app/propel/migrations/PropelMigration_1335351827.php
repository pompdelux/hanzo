<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1335351827.
 * Generated on 2012-04-25 13:03:47 by un
 */
class PropelMigration_1335351827
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
	`currency_code` VARCHAR(12) DEFAULT \'\' NOT NULL
);

ALTER TABLE `orders` DROP `currency_id`;

ALTER TABLE `orders_lines` CHANGE `tax` `vat` DECIMAL(4,2) DEFAULT 0.00;

ALTER TABLE `orders_lines` ADD
(
	`original_price` DECIMAL(15,4)
);

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

ALTER TABLE `orders` ADD
(
	`currency_id` INTEGER NOT NULL
);

ALTER TABLE `orders` DROP `currency_code`;

ALTER TABLE `orders_lines` CHANGE `vat` `tax` DECIMAL(4,2) DEFAULT 0.00;

ALTER TABLE `orders_lines` DROP `original_price`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
