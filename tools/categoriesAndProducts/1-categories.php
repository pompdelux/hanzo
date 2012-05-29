<?php

$from_db = 'pdl_dk';
$to_db = 'hanzo';

mysql_connect('localhost', 'root', '');
mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');

$query = "
  INSERT INTO {$to_db}.categories
  SELECT
    c.categories_id,
    IF(c.parent_id = 0, NULL, c.parent_id),
    c.categories_status,
    c.categories_external_id
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
      WHEN 1 THEN 'en_EN'
      WHEN 7 THEN 'da_DK'
      WHEN 8 THEN 'nl_NL'
    END AS locale,
    c.categories_name,
    c.categories_description
  FROM
    {$from_db}.osc_categories_description AS c
";

mysql_query($query) OR die(mysql_error());

echo "all categories copied\n";
