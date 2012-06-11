<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $from_db = 'pompdelux_se';
  $to_db = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $from_db = 'tmp_oscom_se';
  $to_db = 'tmp_hanzo_se';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');


mysql_query("TRUNCATE TABLE {$to_db}.cms_thread") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.cms_thread_i18n") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.cms") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.cms_i18n") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

mysql_query('SET FOREIGN_KEY_CHECKS = 1');

mysql_query("DELETE FROM {$from_db}.osc_simple_cms WHERE id NOT IN (18, 20, 21)") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("DELETE FROM {$from_db}.osc_simple_cms_i18n WHERE menu_id NOT IN (18, 20, 21)") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("DELETE FROM {$from_db}.osc_simple_cms_item WHERE menu_id NOT IN (18, 20, 21)") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

mysql_query('SET FOREIGN_KEY_CHECKS = 0');

mysql_query("UPDATE {$from_db}.osc_simple_cms SET id = 10 WHERE id = 18") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("UPDATE {$from_db}.osc_simple_cms_i18n SET menu_id = 10 WHERE menu_id = 18") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("UPDATE {$from_db}.osc_simple_cms_item SET menu_id = 10 WHERE menu_id = 18") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

// -----------------------------

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
  WHERE
    f.id IN (10,20,21)
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

// - i18n
$query = "
  INSERT INTO
    {$to_db}.cms_thread_i18n
  SELECT
    f.menu_id,
    CASE f.language_id
      WHEN 9 THEN 'se_SV'
    END AS locale,
    f.title
  FROM
    {$from_db}.osc_simple_cms_i18n AS f
  JOIN
    {$from_db}.osc_simple_cms AS ff
    ON
      (f.menu_id = ff.id)
  WHERE
    f.language_id = 9
    AND
      ff.id IN (10,20,21)
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
  SELECT DISTINCT
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
  JOIN
    {$from_db}.osc_simple_cms_item_i18n AS i
    ON
      (i.menu_item_id = f.id)
  WHERE
    f.menu_id IN (10,20,21)
    AND
      i.language_id = 9
  ORDER BY
    f.id, f.parent_id
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

// - i18n
$query = "
  INSERT INTO
    {$to_db}.cms_i18n(
      id,
      locale,
      title,
      path,
      old_path,
      content,
      settings,
      is_restricted
    )
  SELECT
    f.menu_item_id,
    CASE f.language_id
      WHEN 9 THEN 'se_SV'
    END AS locale,
    f.title,
    f.slug,
    CONCAT('/p/', f.slug),
    f.content,
    f.settings,
    0
  FROM
    {$from_db}.osc_simple_cms_item_i18n AS f
  JOIN
    {$from_db}.osc_simple_cms_item AS ff
    ON
      (f.menu_item_id = ff.id)
  WHERE
    f.language_id = 9
    AND
      ff.menu_id IN (10,20,21)
  ORDER BY
    f.menu_item_id
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

// cleanup
mysql_query("UPDATE {$to_db}.cms_i18n SET content = NULL WHERE content = 'null'") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
mysql_query("UPDATE {$to_db}.cms_i18n SET settings = NULL WHERE settings = ''") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

// replace hd tags
$query = "
  SELECT
    id,
    locale,
    content
  FROM
    {$to_db}.cms_i18n
";

$find = array(
  '{{ SIMPLE_NEWSLETTER_FORM }}',
  '{{ TEXT_EXPECTED_DELIVERY_DATE }}',
  '{{ HD_COSTUMERSERVICE_CONTACT_INFO }}',
);
$replace = array(
  "{{ embed('newsletter_form', {'view':'simple'}) }}",
  "{{ parameter('expected_delivery_date') }}",
  "{{ 'customer.service.contact.info'|trans }}",
);

$result = mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
while($record = mysql_fetch_object($result)) {
  $content = str_replace($find, $replace, stripcslashes($record->content));

  mysql_query(sprintf("UPDATE {$to_db}.cms_i18n SET content = '%s' WHERE id = %d AND locale = '%s'",
    mysql_real_escape_string($content),
    $record->id,
    $record->locale
  )) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
}

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
