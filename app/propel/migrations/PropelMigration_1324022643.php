<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1324022643.
 * Generated on 2011-12-16 09:04:03 by un
 */
class PropelMigration_1324022643
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

CREATE INDEX `products_FI_2` ON `products` (`washing`);

ALTER TABLE `products` ADD CONSTRAINT `products_FK_2`
	FOREIGN KEY (`washing`)
	REFERENCES `products_washing_instructions` (`code`);

ALTER TABLE `products_images_categories_sort` CHANGE `products_images_id` `products_images_id` INTEGER NOT NULL;

CREATE INDEX `I_referenced_products_FK_2_1` ON `products_washing_instructions` (`code`);

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

ALTER TABLE `products` DROP FOREIGN KEY `products_FK_2`;

DROP INDEX `products_FI_2` ON `products`;

ALTER TABLE `products_images_categories_sort` CHANGE `products_images_id` `products_images_id` INTEGER DEFAULT 0 NOT NULL;

DROP INDEX `I_referenced_products_FK_2_1` ON `products_washing_instructions`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}