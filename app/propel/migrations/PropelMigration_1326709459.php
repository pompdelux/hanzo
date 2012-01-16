<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1326709459.
 * Generated on 2012-01-16 11:24:19 by enrique
 */
class PropelMigration_1326709459
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

ALTER TABLE `countries` CHANGE `curency_code` `currency_code` VARCHAR(3) NOT NULL;

ALTER TABLE `countries` CHANGE `curerncy_name` `currency_name` VARCHAR(32) NOT NULL;

DROP INDEX `index3` ON `coupons`;

ALTER TABLE `coupons` ADD
(
	`currency_id` INTEGER NOT NULL
);

ALTER TABLE `coupons` DROP `currency_code`;

CREATE INDEX `index3` ON `coupons` (`currency_id`);

ALTER TABLE `orders` ADD
(
	`currency_id` INTEGER NOT NULL
);

ALTER TABLE `products_domains_prices` ADD
(
	`currency_id` INTEGER NOT NULL
);

ALTER TABLE `products_domains_prices` DROP `currency_code`;

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

ALTER TABLE `countries` CHANGE `currency_code` `curency_code` VARCHAR(3) NOT NULL;

ALTER TABLE `countries` CHANGE `currency_name` `curerncy_name` VARCHAR(32) NOT NULL;

DROP INDEX `index3` ON `coupons`;

ALTER TABLE `coupons` ADD
(
	`currency_code` VARCHAR(3) NOT NULL
);

ALTER TABLE `coupons` DROP `currency_id`;

CREATE INDEX `index3` ON `coupons` (`currency_code`);

ALTER TABLE `orders` DROP `currency_id`;

ALTER TABLE `products_domains_prices` ADD
(
	`currency_code` VARCHAR(3)
);

ALTER TABLE `products_domains_prices` DROP `currency_id`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}