<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1330946717.
 * Generated on 2012-03-05 12:25:17 by un
 */
class PropelMigration_1330946717
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

ALTER TABLE `products_images` DROP FOREIGN KEY `fk_products_images_1`;

ALTER TABLE `products_images` ADD CONSTRAINT `fk_products_images_1`
	FOREIGN KEY (`products_id`)
	REFERENCES `products` (`id`)
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

ALTER TABLE `products_images` DROP FOREIGN KEY `fk_products_images_1`;

ALTER TABLE `products_images` ADD CONSTRAINT `fk_products_images_1`
	FOREIGN KEY (`products_id`)
	REFERENCES `products` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
