<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $db = 'pdl_dk';
  mysql_connect('192.168.2.118', 'pdl_dk_migrate', 'TEMPMIGRATE111');
} else {
  $db = 'tmp_xx';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');


echo "[".date('Y-m-d H:i:s')."] finding customers with no address record but hosts an event.\n";
$query = "
  SELECT
    c.id AS customers_id,
    e.host,
    e.address_line_1,
    e.postal_code,
    e.city,
    e.event_date
  FROM
    {$db}.customers AS c
  JOIN
    {$db}.events AS e
    ON
      (c.id = e.customers_id)
  WHERE
    c.id NOT IN (SELECT DISTINCT customers_id FROM {$db}.addresses)
    AND
      e.event_date > '2012-08-20 00:00:01'
  GROUP BY
    customers_id
";
$insert_query = "
  INSERT INTO
    {$db}.addresses
  SET
    customers_id = '%s',
    first_name = '%s',
    last_name = '%s',
    address_line_1 = '%s',
    postal_code = '%s',
    city = '%s',
    country = '%s',
    countries_id = %d,
    created_at = now(),
    updated_at = now()

";
$result = mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

while ($record = mysql_fetch_object($result)) {
  echo "[".date('Y-m-d H:i:s')."] fixing # {$record->customers_id}'s address record\n";

  @list($first_name, $last_name) = explode(' ', $record->host, 2);

  $query = sprintf($insert_query,
    $record->customers_id,
    mysql_real_escape_string($first_name),
    mysql_real_escape_string($last_name),
    mysql_real_escape_string($record->address_line_1),
    $record->postal_code,
    mysql_real_escape_string($record->city),
    'Denmark',
    58
  );
  mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

}


mysql_query('SET FOREIGN_KEY_CHECKS = 1');
