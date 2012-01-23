<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1327319574.
 * Generated on 2012-01-23 12:52:54 by enrique
 */
class PropelMigration_1327319574
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

DROP INDEX `fk_consultants_info_1` ON `consultants_info`;

CREATE TABLE `gothia_accounts`
(
	`customers_id` INTEGER NOT NULL,
	`first_name` VARCHAR(128) NOT NULL,
	`last_name` VARCHAR(128) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`postal_code` VARCHAR(12) NOT NULL,
	`postal_place` VARCHAR(64) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`phone` VARCHAR(32) NOT NULL,
	`mobile_phone` VARCHAR(32),
	`fax` VARCHAR(32),
	`country_code` VARCHAR(4),
	`distribution_by` VARCHAR(255),
	`distribution_type` VARCHAR(255),
	`social_security_num` VARCHAR(12) NOT NULL,
	PRIMARY KEY (`customers_id`),
	CONSTRAINT `fk_gothia_account_to_customer`
		FOREIGN KEY (`customers_id`)
		REFERENCES `customers` (`id`)
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

DROP TABLE IF EXISTS `gothia_accounts`;

CREATE INDEX `fk_consultants_info_1` ON `consultants_info` (`consultants_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}