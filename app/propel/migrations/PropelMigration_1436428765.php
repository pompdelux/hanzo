<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1436428765.
 * Generated on 2015-07-09 09:59:25 by root
 */
class PropelMigration_1436428765
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

ALTER TABLE `cms_i18n`
    ADD `meta_title` VARCHAR(255) AFTER `only_mobile`,
    ADD `meta_description` VARCHAR(255) AFTER `meta_title`;

CREATE TABLE `products_seo_i18n`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `products_id` INTEGER NOT NULL,
    `meta_title` VARCHAR(255),
    `meta_description` VARCHAR(255),
    `locale` VARCHAR(5) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `FI_products_seo_i18n_1` (`products_id`),
    INDEX `FI_products_seo_i18n_locale_1` (`locale`),
    CONSTRAINT `fk_products_seo_i18n_1`
        FOREIGN KEY (`products_id`)
        REFERENCES `products` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_products_seo_i18n_locale_1`
        FOREIGN KEY (`locale`)
        REFERENCES `languages` (`locale`)
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

DROP TABLE IF EXISTS `products_seo_i18n`;

ALTER TABLE `cms_i18n` DROP `meta_title`;

ALTER TABLE `cms_i18n` DROP `meta_description`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
