<?php

mysql_connect('localhost', 'root', '');
mysql_query('SET NAMES utf8');

$query = "
  INSERT INTO hanzo.categories
  SELECT
    c.categories_id,
    IF(c.parent_id = 0, NULL, c.parent_id),
    c.categories_status,
    c.categories_external_id
  FROM
    pdl_dk.osc_categories as c
  ORDER BY c.parent_id
";

mysql_query($query) OR die(mysql_error());

$query = "
  INSERT INTO hanzo.categories_i18n
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
    pdl_dk.osc_categories_description AS c
";

mysql_query($query) OR die(mysql_error());

echo "all categories copied\n";
