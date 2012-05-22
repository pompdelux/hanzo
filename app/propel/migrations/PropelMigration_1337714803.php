<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1337714803.
 * Generated on 2012-05-22 21:26:43 by un
 */
class PropelMigration_1337714803
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

ALTER TABLE `mannequin_images` DROP FOREIGN KEY `fk_mannequin_images_1`;

ALTER TABLE `mannequin_images` ADD CONSTRAINT `fk_mannequin_images_1`
	FOREIGN KEY (`master`)
	REFERENCES `products` (`sku`)
	ON DELETE CASCADE;

ALTER TABLE `orders` DROP FOREIGN KEY `orders_FK_1`;

ALTER TABLE `orders` ADD CONSTRAINT `orders_FK_1`
	FOREIGN KEY (`customers_id`)
	REFERENCES `customers` (`id`)
	ON UPDATE CASCADE
	ON DELETE SET NULL;

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

ALTER TABLE `mannequin_images` DROP FOREIGN KEY `fk_mannequin_images_1`;

ALTER TABLE `mannequin_images` ADD CONSTRAINT `fk_mannequin_images_1`
	FOREIGN KEY (`master`)
	REFERENCES `products` (`sku`);

ALTER TABLE `orders` DROP FOREIGN KEY `orders_FK_1`;

ALTER TABLE `orders` ADD CONSTRAINT `orders_FK_1`
	FOREIGN KEY (`customers_id`)
	REFERENCES `customers` (`id`)
	ON DELETE SET NULL;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
