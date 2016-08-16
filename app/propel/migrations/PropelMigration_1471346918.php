<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1471346918.
 * Generated on 2016-08-16 13:28:38 by root
 */
class PropelMigration_1471346918
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
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `customers`
    ADD `may_be_contacted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `discount`;

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
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `customers` DROP `may_be_contacted`;

SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
