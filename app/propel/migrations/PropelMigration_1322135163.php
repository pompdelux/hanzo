<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1322135163.
 * Generated on 2011-11-24 12:46:03 by un
 */
class PropelMigration_1322135163
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

CREATE TABLE `cms_thread`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `cms`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER,
	`cms_thread_id` INTEGER NOT NULL,
	`type` VARCHAR(255) DEFAULT \'cms\' NOT NULL,
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `FI_cms_1` (`cms_thread_id`),
	INDEX `FI_cms_2` (`parent_id`),
	CONSTRAINT `fk_cms_1`
		FOREIGN KEY (`cms_thread_id`)
		REFERENCES `cms_thread` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `fk_cms_2`
		FOREIGN KEY (`parent_id`)
		REFERENCES `cms` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `cms_thread_i18n`
(
	`id` INTEGER NOT NULL,
	`locale` VARCHAR(5) DEFAULT \'en_EN\' NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`,`locale`),
	CONSTRAINT `cms_thread_i18n_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `cms_thread` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `cms_i18n`
(
	`id` INTEGER NOT NULL,
	`locale` VARCHAR(5) DEFAULT \'en_EN\' NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`path` VARCHAR(255) NOT NULL,
	`content` TEXT,
	PRIMARY KEY (`id`,`locale`),
	CONSTRAINT `cms_i18n_FK_1`
		FOREIGN KEY (`id`)
		REFERENCES `cms` (`id`)
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

DROP TABLE IF EXISTS `cms_thread`;

DROP TABLE IF EXISTS `cms`;

DROP TABLE IF EXISTS `cms_thread_i18n`;

DROP TABLE IF EXISTS `cms_i18n`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}