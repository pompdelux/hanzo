<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1412335376.
 * Generated on 2014-10-03 13:22:56 by un
 */
class PropelMigration_1412335376
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

CREATE TABLE `wishlists`
(
    `id` VARCHAR(5) NOT NULL,
    `customers_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `wishlists_FI_1` (`customers_id`),
    CONSTRAINT `wishlists_FK_1`
        FOREIGN KEY (`customers_id`)
        REFERENCES `customers` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `wishlists_lines`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `wishlists_id` VARCHAR(5) NOT NULL,
    `products_id` INTEGER,
    `quantity` INTEGER,
    PRIMARY KEY (`id`),
    INDEX `FI_wishlists_lines_1` (`wishlists_id`),
    INDEX `FI_wishlists_lines_2` (`products_id`),
    CONSTRAINT `fk_wishlists_lines_1`
        FOREIGN KEY (`wishlists_id`)
        REFERENCES `wishlists` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_wishlists_lines_2`
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

DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `wishlists_lines`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
