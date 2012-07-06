<?php // æåå

if (isset($argv[1]) && $argv[1] == 'live') {
  $from_db = 'pompdelux_se';
  $to_db = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $from_db = 'pdl_dk';
  $to_db = 'hanzo';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

echo "[".date('Y-m-d H:i:s')."] cleaning tables\n"; flush();

mysql_query("TRUNCATE TABLE {$to_db}.wall") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.wall_likes") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

echo "[".date('Y-m-d H:i:s')."] migrating wall posts\n"; flush();

$query = "
  INSERT INTO
    {$to_db}.wall (
      id,
      parent_id,
      customers_id,
      messate,
      status,
      created_at,
      updated_at
    )
  SELECT
    id,
    parent_id,
    customers_id,
    message,
    status,
    created_at,
    updated_at
  FROM
    {$from_db}.pdl_wall
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

echo "[".date('Y-m-d H:i:s')."] migrating wall likes/dislikes\n"; flush();

$query = "
  INSERT INTO
    {$to_db}.wall_likes (
      id,
      wall_id,
      customers_id,
      status
    )
  SELECT
    id,
    wall_id,
    customers_id,
    status
  FROM
    {$from_db}.pdl_wall_likes
";

mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

echo "[".date('Y-m-d H:i:s')."] done\n"; flush();


mysql_query('SET FOREIGN_KEY_CHECKS = 1');
