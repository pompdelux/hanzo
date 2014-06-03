<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1401732354.
 * Generated on 2014-06-02 20:05:54 by un
 */
class PropelMigration_1401732354
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

ALTER TABLE `products_quantity_discount`
    ADD `valid_from` DATE AFTER `discount`,
    ADD `valid_to` DATE AFTER `valid_from`;

CREATE INDEX `pqd_date_index` ON `products_quantity_discount` (`valid_from`,`valid_to`);

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

DROP INDEX `pqd_date_index` ON `products_quantity_discount`;
ALTER TABLE `products_quantity_discount` DROP `valid_from`;
ALTER TABLE `products_quantity_discount` DROP `valid_to`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
