<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1327659588.
 * Generated on 2012-01-27 11:19:48 by un
 */
class PropelMigration_1327659588
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

CREATE TABLE `messages`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`ns` VARCHAR(12) NOT NULL,
	`key` VARCHAR(128) NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `key_UNIQUE` (`ns`, `key`)
) ENGINE=InnoDB;

CREATE TABLE `messages_i18n`
(
	`id` INTEGER NOT NULL,
	`locale` VARCHAR(5) DEFAULT \'en_EN\' NOT NULL,
	`subject` VARCHAR(255) NOT NULL,
	`body` TEXT,
	PRIMARY KEY (`id`,`locale`),
	CONSTRAINT `messages_i18n_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `messages` (`id`)
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

DROP TABLE IF EXISTS `messages`;

DROP TABLE IF EXISTS `messages_i18n`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}