<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1357657040.
 * Generated on 2013-01-08 15:57:20 by andersbryrup
 */
class PropelMigration_1357657040
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

CREATE INDEX `cms_I_1` ON `cms` (`path`);

CREATE INDEX `cms_I_2` ON `cms` (`old_path`);

DROP INDEX `FI_orders_attributes_1` ON `orders_attributes`;

ALTER TABLE `products_images_to_looks`
    ADD `sort` INTEGER AFTER `looks_id`;

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

DROP INDEX `cms_I_1` ON `cms`;

DROP INDEX `cms_I_2` ON `cms`;

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

ALTER TABLE `products_images_to_looks` DROP `sort`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}