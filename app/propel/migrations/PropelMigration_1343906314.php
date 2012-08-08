<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1343906314.
 * Generated on 2012-08-02 13:18:34 
 */
class PropelMigration_1343906314
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

ALTER TABLE `addresses` DROP `sub_type`;

CREATE TABLE `consultant_newsletter_drafts`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`consultants_id` INTEGER NOT NULL,
	`subject` VARCHAR(255) NOT NULL,
	`content` TEXT,
	PRIMARY KEY (`id`),
	INDEX `consultant_newsletter_drafts_FI_1` (`consultants_id`),
	CONSTRAINT `consultant_newsletter_drafts_FK_1`
		FOREIGN KEY (`consultants_id`)
		REFERENCES `customers` (`id`)
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

DROP TABLE IF EXISTS `consultant_newsletter_drafts`;

ALTER TABLE `addresses` ADD
(
	`sub_type` VARCHAR(32)
);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
