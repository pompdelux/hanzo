<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $from = 'pompdelux_se';
  $to = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $from = 'tmp_oscom_se';
  $to = 'tmp_hanzo_se';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');


$result = mysql_query("
  INSERT INTO {$to}.gothia_accounts (
    customers_id,
    social_security_num,
    distribution_by,
    distribution_type
  )
  SELECT
    DISTINCT c.customers_id,
    a1.attribute_value,
    a2.attribute_value,
    a3.attribute_value
  FROM
    {$from}.osc_customers_attributes AS c
  JOIN
    {$from}.osc_customers_attributes AS a1
      ON(c.customers_id = a1.customers_id AND a1.attribute_key = 'CI_Organization_PersonalNo')
  JOIN
    {$from}.osc_customers_attributes AS a2
      ON(c.customers_id = a2.customers_id AND a2.attribute_key = 'DistributionBy')
  JOIN
    {$from}.osc_customers_attributes AS a3
      ON(c.customers_id = a3.customers_id AND a3.attribute_key = 'DistributionType')
") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));


// echo "[".date('Y-m-d H:i:s')."] creating table\n";
// mysql_query("CREATE TABLE {$to}.customers_gotia_attributes(
//   customers_id INTEGER NOT NULL,
//   c_key VARCHAR(255) NOT NULL,
//   c_value VARCHAR(255) NOT NULL,
//   PRIMARY KEY (customers_id,c_key),
//   CONSTRAINT customers_gotia_attributes_FK_1
//     FOREIGN KEY (customers_id)
//     REFERENCES customers (id)
//     ON DELETE CASCADE
// ) ENGINE=InnoDB;
// ") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));


// // migrate customers
// echo "[".date('Y-m-d H:i:s')."] porting info ....\n";
// $query = "
//   INSERT INTO {$to}.customers_gotia_attributes (
//     customers_id,
//     c_key,
//     c_value
//   )
//   SELECT
//     a.customers_id,
//     a.attribute_key,
//     a.attribute_value
//   FROM
//     {$from}.osc_customers AS c
//     JOIN
//       {$from}.osc_customers_attributes AS a
//       ON
//         (c.customers_id = a.customers_id)
// ";
// mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
echo "[".date('Y-m-d H:i:s')."] - done -\n\n";
