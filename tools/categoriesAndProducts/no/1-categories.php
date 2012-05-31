<?php

$from_db = 'tmp_oscom_no';
$to_db = 'tmp_hanzo_no';

mysql_connect('localhost', 'root', '');

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

mysql_query("TRUNCATE TABLE {$to_db}.categories") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.categories_i18n") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));


$query = "
  INSERT INTO {$to_db}.categories
  SELECT
    c.categories_id,
    IF(c.parent_id = 0, NULL, c.parent_id),
    c.categories_external_id,
    c.categories_status
  FROM
    {$from_db}.osc_categories as c
  ORDER BY c.parent_id
";

mysql_query($query) OR die(mysql_error());

$query = "
  INSERT INTO {$to_db}.categories_i18n
  SELECT
    c.categories_id,
    CASE c.language_id
      WHEN 8 THEN 'no_NB'
    END AS locale,
    c.categories_name,
    c.categories_description
  FROM
    {$from_db}.osc_categories_description AS c
";

mysql_query($query) or die('Line: '.__LINE__."\n".mysql_error()."\n");

mysql_query('SET FOREIGN_KEY_CHECKS = 1');

echo "all categories copied\n";

