<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $db = 'pdl_dk';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $db = 'hanzo';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');


echo "[".date('Y-m-d H:i:s')."] finding all customers with only one address, wich is of type: shipping\n";
$query = "
  select
    customers_id,
    type,
    count(type) as num
  from
    {$db}.addresses
  group by customers_id
  having
    num = 1
    and
    type = 'shipping'
  ORDER BY
    type DESC
";
$result = mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

while ($record = mysql_fetch_object($result)) {
  echo "[".date('Y-m-d H:i:s')."] setting customer: {$record->customers_id}'s address type to: payment\n";
  $query = "
    UPDATE
      {$db}.addresses
    SET
      `type` = 'payment'
    WHERE
      customers_id = {$record->customers_id}
      AND
        `type` = 'shipping'
  ";
  mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

}


mysql_query('SET FOREIGN_KEY_CHECKS = 1');
