<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1384847643.
 * Generated on 2013-11-19 08:54:03 by un
 */
class PropelMigration_1384847643
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

CREATE TABLE `search_products_tags`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `master_products_id` INTEGER NOT NULL,
    `products_id` INTEGER NOT NULL,
    `token` VARCHAR(128) NOT NULL,
    `locale` VARCHAR(12) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `index_token_locale` (`token`, `locale`),
    INDEX `FI_products_images_1` (`master_products_id`),
    INDEX `FI_products_images_2` (`products_id`),
    CONSTRAINT `fk_spt_products_images_1`
        FOREIGN KEY (`master_products_id`)
        REFERENCES `products` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_spt_products_images_2`
        FOREIGN KEY (`products_id`)
        REFERENCES `products` (`id`)
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

DROP TABLE IF EXISTS `search_products_tags`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
