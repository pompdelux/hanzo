<?php


if (isset($argv[1]) && $argv[1] == 'live') {
  $to_db = 'pdl_no';
  mysql_connect('192.168.2.136', 'pdl_no_migrate', 'TEMPMIGRATE111');
} else {
  $to_db = 'tmp_hanzo_no';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

echo "[".date('Y-m-d H:i:s')."] stating out of stock sync.\n";

$query = "SELECT sku FROM {$to_db}.products WHERE master IS NULL";
$result = mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

$count = 0;
while ($product = mysql_fetch_object($result)) {
  echo "."; flush();
  $quantity = mysql_fetch_object(mysql_query("SELECT SUM(s.quantity) AS `sum` FROM {$to_db}.products_stock AS s WHERE s.products_id IN( SELECT p.id FROM {$to_db}.products AS p WHERE p.sku = '{$product->sku}' OR p.master = '{$product->sku}')")) or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

  if ($quantity->sum == 0) {
    mysql_query("UPDATE {$to_db}.products SET is_out_of_stock = 1 WHERE sku = '{$product->sku}' OR master = '{$product->sku}'") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
    $count++;
  }
}

mysql_query('SET FOREIGN_KEY_CHECKS = 1');

echo "\n[".date('Y-m-d H:i:s')."] all done, {$count} products out of stock\n";
