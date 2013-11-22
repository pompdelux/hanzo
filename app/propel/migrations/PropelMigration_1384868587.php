<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1384868587.
 * Generated on 2013-11-19 14:43:07 by un
 */
class PropelMigration_1384868587
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

CREATE TABLE `search_fulltext`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `type` VARCHAR(12) DEFAULT \'cms\' NOT NULL,
    `target` VARCHAR(255) NOT NULL,
    `locale` VARCHAR(12) NOT NULL,
    `content` TEXT,
    PRIMARY KEY (`id`),
    FULLTEXT INDEX `sf_fulltext_index` (`content`),
    INDEX `sf_locale_index` (`locale`)
) ENGINE=MyISAM;

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

DROP TABLE IF EXISTS `search_fulltext`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
