<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1336485886.
 * Generated on 2012-05-08 16:04:46 by un
 */
class PropelMigration_1336485886
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

ALTER TABLE `categories_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'da_DK\' NOT NULL;

ALTER TABLE `cms_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'da_DK\' NOT NULL;

ALTER TABLE `cms_thread_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'da_DK\' NOT NULL;

ALTER TABLE `messages_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'da_DK\' NOT NULL;

ALTER TABLE `products_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'da_DK\' NOT NULL;

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

ALTER TABLE `categories_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'en_EN\' NOT NULL;

ALTER TABLE `cms_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'en_EN\' NOT NULL;

ALTER TABLE `cms_thread_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'en_EN\' NOT NULL;

ALTER TABLE `messages_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'en_EN\' NOT NULL;

ALTER TABLE `products_i18n` CHANGE `locale` `locale` VARCHAR(5) DEFAULT \'en_EN\' NOT NULL;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
