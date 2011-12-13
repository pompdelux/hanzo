<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1323549052.
 * Generated on 2011-12-10 21:30:52 by un
 */
class PropelMigration_1323549052
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

ALTER TABLE `products_washing_instructions` DROP FOREIGN KEY `fk_products_washing_instructions_1`;

DROP INDEX `FI_products_washing_instructions_1` ON `products_washing_instructions`;

ALTER TABLE `products_washing_instructions` ADD
(
	`locale` VARCHAR(5) NOT NULL
);

ALTER TABLE `products_washing_instructions` DROP `languages_id`;

CREATE INDEX `FI_products_washing_instructions_1` ON `products_washing_instructions` (`locale`);

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

ALTER TABLE `products_washing_instructions` DROP FOREIGN KEY `fk_products_washing_instructions_1`;

DROP INDEX `FI_products_washing_instructions_1` ON `products_washing_instructions`;

ALTER TABLE `products_washing_instructions` ADD
(
	`languages_id` INTEGER NOT NULL
);

ALTER TABLE `products_washing_instructions` DROP `locale`;

CREATE INDEX `FI_products_washing_instructions_1` ON `products_washing_instructions` (`languages_id`);

ALTER TABLE `products_washing_instructions` ADD CONSTRAINT `fk_products_washing_instructions_1`
	FOREIGN KEY (`languages_id`)
	REFERENCES `languages` (`id`)
	ON DELETE CASCADE;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}