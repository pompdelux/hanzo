<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1350374539.
 * Generated on 2012-10-16 10:02:19 by un
 */
class PropelMigration_1350374539
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

DROP TABLE IF EXISTS `coupons_to_customers`;

ALTER TABLE `coupons` ADD (
	`is_active` TINYINT(1) DEFAULT 1 NOT NULL
);

ALTER TABLE `coupons` DROP `vat`;
ALTER TABLE `coupons` DROP `uses_pr_coupon`;
ALTER TABLE `coupons` DROP `uses_pr_coustomer`;
ALTER TABLE `orders` DROP FOREIGN KEY `orders_FK_4`;

ALTER TABLE `orders` ADD CONSTRAINT `orders_FK_4`
	FOREIGN KEY (`events_id`)
	REFERENCES `events` (`id`)
	ON UPDATE CASCADE
	ON DELETE RESTRICT;

CREATE TABLE `orders_to_coupons` (
	`orders_id` INTEGER NOT NULL,
	`coupons_id` INTEGER NOT NULL,
	`amount` DECIMAL(15,4) NOT NULL,
	PRIMARY KEY (`orders_id`,`coupons_id`),
	INDEX `orders_to_coupons_FI_1` (`coupons_id`),
	CONSTRAINT `orders_to_coupons_FK_1`
		FOREIGN KEY (`coupons_id`)
		REFERENCES `coupons` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `orders_to_coupons_FK_2`
		FOREIGN KEY (`orders_id`)
		REFERENCES `orders` (`id`)
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

DROP TABLE IF EXISTS `orders_to_coupons`;

ALTER TABLE `coupons` ADD (
	`vat` DECIMAL(15,4),
	`uses_pr_coupon` INTEGER DEFAULT 1 NOT NULL,
	`uses_pr_coustomer` INTEGER DEFAULT 1 NOT NULL
);

ALTER TABLE `coupons` DROP `is_active`;
ALTER TABLE `orders` DROP FOREIGN KEY `orders_FK_4`;

ALTER TABLE `orders` ADD CONSTRAINT `orders_FK_4`
	FOREIGN KEY (`events_id`)
	REFERENCES `events` (`id`)
	ON UPDATE CASCADE;

CREATE TABLE `coupons_to_customers` (
	`coupons_id` INTEGER NOT NULL,
	`customers_id` INTEGER NOT NULL,
	`use_count` INTEGER DEFAULT 0 NOT NULL,
	PRIMARY KEY (`coupons_id`,`customers_id`),
	INDEX `FI_coupons_to_customers_1` (`customers_id`),
	CONSTRAINT `fk_coupons_to_customers_1`
		FOREIGN KEY (`customers_id`)
		REFERENCES `customers` (`id`)
		ON DELETE CASCADE,
	CONSTRAINT `fk_coupons_to_customers_2`
		FOREIGN KEY (`coupons_id`)
		REFERENCES `coupons` (`id`)
		ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
	}

}
