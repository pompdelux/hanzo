<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1358495834.
 * Generated on 2013-01-18 08:57:14 by andersbryrup
 */
class PropelMigration_1358495834
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

ALTER TABLE `events` DROP FOREIGN KEY `fk_events_1`;

ALTER TABLE `events` ADD CONSTRAINT `fk_events_1`
    FOREIGN KEY (`consultants_id`)
    REFERENCES `consultants` (`id`);

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

ALTER TABLE `events` DROP FOREIGN KEY `fk_events_1`;

ALTER TABLE `events` ADD CONSTRAINT `fk_events_1`
    FOREIGN KEY (`consultants_id`)
    REFERENCES `customers` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}