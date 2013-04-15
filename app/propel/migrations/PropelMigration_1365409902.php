<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1365409902.
 * Generated on 2013-04-08 10:31:42 by un
 */
class PropelMigration_1365409902
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

ALTER TABLE `addresses`
    ADD `external_address_id` VARCHAR(128) AFTER `company_name`;


ALTER TABLE `orders`
    ADD `billing_external_address_id` VARCHAR(128) AFTER `billing_method`,
    ADD `delivery_external_address_id` VARCHAR(128) AFTER `delivery_method`;

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

ALTER TABLE `addresses` DROP `external_address_id`;
ALTER TABLE `orders` DROP `billing_external_address_id`;
ALTER TABLE `orders` DROP `delivery_external_address_id`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}
