<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1321992609.
 * Generated on 2011-11-22 21:10:09 by un
 */
class PropelMigration_1321992609
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


DROP INDEX `domains_settings_unique` ON `domains_settings`;

ALTER TABLE `domains_settings` CHANGE `namespace` `ns` VARCHAR(64) NOT NULL;

CREATE UNIQUE INDEX `domains_settings_unique` ON `domains_settings` (`c_key`,`ns`);

ALTER TABLE `orders_attributes` DROP PRIMARY KEY;

ALTER TABLE `orders_attributes` CHANGE `namespace` `ns` VARCHAR(64) NOT NULL;

ALTER TABLE `orders_attributes` ADD PRIMARY KEY (`c_key`,`ns`);

ALTER TABLE `settings` DROP PRIMARY KEY;

ALTER TABLE `settings` CHANGE `namespace` `ns` VARCHAR(64) NOT NULL;

ALTER TABLE `settings` ADD PRIMARY KEY (`c_key`,`ns`);

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

DROP INDEX `domains_settings_unique` ON `domains_settings`;

ALTER TABLE `domains_settings` CHANGE `ns` `namespace` VARCHAR(64) NOT NULL;

CREATE UNIQUE INDEX `domains_settings_unique` ON `domains_settings` (`c_key`,`namespace`);

ALTER TABLE `orders_attributes` DROP PRIMARY KEY;

ALTER TABLE `orders_attributes` CHANGE `ns` `namespace` VARCHAR(64) NOT NULL;

ALTER TABLE `orders_attributes` ADD PRIMARY KEY (`c_key`,`namespace`);

ALTER TABLE `settings` DROP PRIMARY KEY;

ALTER TABLE `settings` CHANGE `ns` `namespace` VARCHAR(64) NOT NULL;

ALTER TABLE `settings` ADD PRIMARY KEY (`c_key`,`namespace`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
