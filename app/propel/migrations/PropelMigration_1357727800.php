<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1357727800.
 * Generated on 2013-01-09 11:36:40 by andersbryrup
 */
class PropelMigration_1357727800
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

DROP TABLE IF EXISTS `looks`;

DROP TABLE IF EXISTS `looks_i18n`;

DROP TABLE IF EXISTS `products_images_to_looks`;

CREATE INDEX `FI_products_images_categories_sort_3` ON `products_images_categories_sort` (`categories_id`);

ALTER TABLE `products_images_categories_sort` ADD CONSTRAINT `fk_products_images_categories_sort_3`
    FOREIGN KEY (`categories_id`)
    REFERENCES `categories` (`id`)
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

ALTER TABLE `products_images_categories_sort` DROP FOREIGN KEY `fk_products_images_categories_sort_3`;

DROP INDEX `FI_products_images_categories_sort_3` ON `products_images_categories_sort`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}