<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1357656716.
 * Generated on 2013-01-08 15:51:56 by andersbryrup
 */
class PropelMigration_1357656716
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

CREATE TABLE `looks`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `products_images_to_looks`
(
    `products_images_id` INTEGER NOT NULL,
    `looks_id` INTEGER NOT NULL,
    PRIMARY KEY (`products_images_id`,`looks_id`),
    INDEX `FI_ducts_images_to_looks_ibfk_2` (`looks_id`),
    CONSTRAINT `products_images_to_looks_ibfk_1`
        FOREIGN KEY (`products_images_id`)
        REFERENCES `products_images` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `products_images_to_looks_ibfk_2`
        FOREIGN KEY (`looks_id`)
        REFERENCES `looks` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `looks_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT \'da_DK\' NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `looks_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `looks` (`id`)
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

DROP TABLE IF EXISTS `looks`;

DROP TABLE IF EXISTS `products_images_to_looks`;

DROP TABLE IF EXISTS `looks_i18n`;

DROP INDEX `cms_I_1` ON `cms`;

DROP INDEX `cms_I_2` ON `cms`;

CREATE INDEX `FI_orders_attributes_1` ON `orders_attributes` (`orders_id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}