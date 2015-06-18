<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1433246512.
 * Generated on 2015-06-02 14:01:52 by root
 */
class PropelMigration_1433246512
{

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        $pdo = $manager->getPdoConnection('default');

        $sql = "SELECT c_value FROM settings WHERE ns = 'core' AND c_key = 'active_product_range'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $range = false;
        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $range = $record['c_value'];
        }

        if ($range === false) {
            return false;
        }

        $sql = "SELECT domain_key FROM domains";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $domains = [];
        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $domains[] = $record['domain_key'];
        }

        if (empty($domains)) {
            return false;
        }

        $sql = "INSERT INTO domains_settings(domain_key, c_key, ns, c_value) VALUES(:domain_key, :c_key, :ns, :c_value)";
        $stmt = $pdo->prepare($sql);

        foreach ($domains as $domain)
        {
            $stmt->execute([
                'domain_key' => $domain,
                'c_key'      => 'active_product_range',
                'ns'         => 'core',
                'c_value'    => $range,
                ]);
        }

        $sql = "DELETE FROM settings WHERE ns = 'core' AND c_key = 'active_product_range'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
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
        return array ();
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array ();
    }

}
