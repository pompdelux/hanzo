<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1371648173.
 * Generated on 2013-06-19 15:22:53 by un
 */
class PropelMigration_1371648173
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

DROP INDEX `index3` ON `coupons`;

ALTER TABLE `coupons`
    ADD `min_purchase_amount` DECIMAL(15,4) AFTER `amount`,
    ADD `is_used` TINYINT(1) DEFAULT 0 NOT NULL AFTER `is_active`;

CREATE INDEX `index3` ON `coupons` (`active_from`,`active_to`);
CREATE INDEX `index4` ON `coupons` (`is_active`);
CREATE INDEX `index5` ON `coupons` (`is_used`);
CREATE INDEX `index6` ON `coupons` (`currency_code`);

CREATE TABLE `gift_cards`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(12) NOT NULL,
    `amount` DECIMAL(15,4) NOT NULL,
    `currency_code` VARCHAR(3) NOT NULL,
    `active_from` DATETIME,
    `active_to` DATETIME,
    `is_active` TINYINT(1) DEFAULT 1 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `code_UNIQUE` (`code`),
    INDEX `index3` (`active_from`, `active_to`),
    INDEX `index4` (`is_active`),
    INDEX `index5` (`currency_code`)
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

DROP TABLE IF EXISTS `gift_cards`;

DROP INDEX `index4` ON `coupons`;
DROP INDEX `index5` ON `coupons`;
DROP INDEX `index6` ON `coupons`;
DROP INDEX `index3` ON `coupons`;

ALTER TABLE `coupons` DROP `min_purchase_amount`;
ALTER TABLE `coupons` DROP `is_used`;

CREATE INDEX `index3` ON `coupons` (`currency_code`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
