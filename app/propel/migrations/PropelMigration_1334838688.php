<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1334838688.
 * Generated on 2012-04-19 14:31:28 by un
 */
class PropelMigration_1334838688
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

ALTER TABLE `cms_i18n` ADD
(
	`is_restricted` TINYINT(1) DEFAULT 0 NOT NULL
);

ALTER TABLE `products_washing_instructions` DROP FOREIGN KEY `products_washing_instructions_ibfk_1`;

ALTER TABLE `products_washing_instructions` ADD CONSTRAINT `fk_products_washing_instructions_1`
	FOREIGN KEY (`locale`)
	REFERENCES `languages` (`locale`)
	ON DELETE CASCADE;

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

ALTER TABLE `cms_i18n` DROP `is_restricted`;

ALTER TABLE `products_washing_instructions` DROP FOREIGN KEY `fk_products_washing_instructions_1`;

ALTER TABLE `products_washing_instructions` ADD CONSTRAINT `products_washing_instructions_ibfk_1`
	FOREIGN KEY (`locale`)
	REFERENCES `languages` (`locale`)
	ON UPDATE CASCADE
	ON DELETE CASCADE;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
