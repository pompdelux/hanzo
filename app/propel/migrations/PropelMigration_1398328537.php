<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1398328537.
 * Generated on 2014-04-24 10:35:37 by un
 */
class PropelMigration_1398328537
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

ALTER TABLE `products`
    ADD `primary_categories_id` INTEGER AFTER `is_voucher`;

CREATE INDEX `products_FI_3` ON `products` (`primary_categories_id`);

ALTER TABLE `products` ADD CONSTRAINT `products_FK_3`
    FOREIGN KEY (`primary_categories_id`)
    REFERENCES `categories` (`id`)
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

ALTER TABLE `products` DROP FOREIGN KEY `products_FK_3`;

DROP INDEX `products_FI_3` ON `products`;

ALTER TABLE `products` DROP `primary_categories_id`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
