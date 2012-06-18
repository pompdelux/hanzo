<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $from = 'pompdelux_se';
  $to = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $from = 'tmp_oscom';
  $to = 'hanzo';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

echo "[".date('Y-m-d H:i:s')."] migrating attachments:\n";

$res = mysql_query("SELECT * FROM {$from}.osc_orders_attributes WHERE attribute_key LIKE 'attachment_%'");

$attributes_sql = "
  INSERT INTO
    {$to}.orders_attributes
  SET
    orders_id = %d,
    ns = '%s',
    c_key = '%s',
    c_value = '%s'
  ON DUPLICATE KEY UPDATE
    c_value = '%4\$s'
";

while ($r = mysql_fetch_object($res)) {
  echo '.'; flush();

  $orders_attributes[$r->attribute_key] = $r->attribute_value;

  mysql_query(sprintf($attributes_sql,
    $r->orders_id,
    'attachment',
    $r->attribute_key,
    $r->attribute_value
  )) or die('Line: '.__LINE__."\n".mysql_error());

}

echo "\n[".date('Y-m-d H:i:s')."] done!\n";

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
