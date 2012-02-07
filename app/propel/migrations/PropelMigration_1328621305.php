<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1328621305.
 * Generated on 2012-02-07 14:28:25 by un
 */
class PropelMigration_1328621305
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

ALTER TABLE `consultants` ADD CONSTRAINT `consultants_FK_1`
	FOREIGN KEY (`id`)
	REFERENCES `customers` (`id`)
	ON DELETE CASCADE;

ALTER TABLE `customers` DROP FOREIGN KEY `customers_FK_2`;

ALTER TABLE `customers` CHANGE `id` `id` INTEGER NOT NULL;

ALTER TABLE `orders` CHANGE `id` `id` INTEGER NOT NULL;

DROP INDEX `FI_orders_attributes_1` ON `orders_attributes`;

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

ALTER TABLE `consultants` DROP FOREIGN KEY `consultants_FK_1`;

ALTER TABLE `customers` CHANGE `id` `id` INTEGER NOT NULL AUTO_INCREMENT;

ALTER TABLE `customers` ADD CONSTRAINT `customers_FK_2`
	FOREIGN KEY (`id`)
	REFERENCES `consultants` (`id`)
	ON DELETE CASCADE;

ALTER TABLE `orders` CHANGE `id` `id` INTEGER NOT NULL AUTO_INCREMENT;

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}