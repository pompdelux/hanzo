<?php // æåå

$from_db = 'pdl_dk';
$to_db = 'hanzo';

mysql_connect('localhost', 'root', '');
mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');

mysql_query('SET FOREIGN_KEY_CHECKS = 1');

$query = "DELETE FROM {$to_db}.cms_thread WHERE id IN (10, 20, 21)";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$query = "DELETE FROM {$to_db}.cms WHERE cms_thread_id IN (10, 20, 21)";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

mysql_query('SET FOREIGN_KEY_CHECKS = 0');


// recreate threads
$query = "
  INSERT INTO
    {$to_db}.cms_thread (
      id,
      is_active
    )
  SELECT
    f.id,
    f.is_active
  FROM
    {$from_db}.osc_simple_cms AS f
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

// - i18n
$query = "
  INSERT INTO
    {$to_db}.cms_thread_i18n
  SELECT
    f.menu_id,
    CASE f.language_id
      WHEN 1 THEN 'en_EN'
      WHEN 7 THEN 'da_DK'
      WHEN 8 THEN 'nl_NL'
    END AS locale,
    f.title
  FROM
    {$from_db}.osc_simple_cms_i18n AS f
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));



// recreate items
$query = "
  INSERT INTO
    {$to_db}.cms (
      id,
      parent_id,
      cms_thread_id,
      type,
      is_active,
      created_at,
      updated_at,
      sort
    )
  SELECT
    f.id,
    f.parent_id,
    f.menu_id,
    f.type,
    f.is_active,
    f.created_at,
    f.created_at,
    f.sort_order
  FROM
    {$from_db}.osc_simple_cms_item AS f
  ORDER BY
    f.id, f.parent_id
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

// - i18n
$query = "
  INSERT INTO
    {$to_db}.cms_i18n
  SELECT
    f.menu_item_id,
    CASE f.language_id
      WHEN 1 THEN 'en_EN'
      WHEN 7 THEN 'da_DK'
      WHEN 8 THEN 'nl_NL'
    END AS locale,
    f.title,
    f.slug,
    f.content,
    f.settings
  FROM
    {$from_db}.osc_simple_cms_item_i18n AS f
  ORDER BY
    f.menu_item_id
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

// cleanup
mysql_query("UPDATE {$to_db}.cms_i18n SET content = NULL WHERE content = 'null'") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
mysql_query("UPDATE {$to_db}.cms_i18n SET settings = NULL WHERE settings = ''") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
