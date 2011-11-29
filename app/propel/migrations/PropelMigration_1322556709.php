<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1322556709.
 * Generated on 2011-11-29 09:51:49 by un
 */
class PropelMigration_1322556709
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

ALTER TABLE `products_domains_prices` DROP PRIMARY KEY;

ALTER TABLE `products_domains_prices` CHANGE `from_date` `from_date` DATETIME NOT NULL;

ALTER TABLE `products_domains_prices` CHANGE `to_date` `to_date` DATETIME;

ALTER TABLE `products_domains_prices` ADD PRIMARY KEY (`products_id`,`domains_id`,`from_date`);

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

ALTER TABLE `products_domains_prices` DROP PRIMARY KEY;

ALTER TABLE `products_domains_prices` CHANGE `from_date` `from_date` DATE;

ALTER TABLE `products_domains_prices` CHANGE `to_date` `to_date` DATE;

ALTER TABLE `products_domains_prices` ADD PRIMARY KEY (`products_id`,`domains_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}