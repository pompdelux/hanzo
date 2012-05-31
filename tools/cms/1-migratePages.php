<?php // æåå

$from_db = 'tmp_oscom_se';
$to_db = 'tmp_hanzo_se';

mysql_connect('localhost', 'root', '');
mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');

mysql_query('SET FOREIGN_KEY_CHECKS = 0');


mysql_query("TRUNCATE TABLE {$to_db}.cms_thread") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.cms_thread_i18n") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.cms") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.cms_i18n") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));



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
--      WHEN 1 THEN 'en_GB'
--      WHEN 7 THEN 'da_DK'
--      WHEN 8 THEN 'nl_NL'
      WHEN 9 THEN 'sv_SE'
    END AS locale,
    f.title
  FROM
    {$from_db}.osc_simple_cms_i18n AS f
  WHERE
    f.language_id = 9
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
  JOIN
    {$from_db}.osc_simple_cms_item_i18n AS i
    ON
      (i.menu_item_id = f.id AND i.language_id = 9)
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
--      WHEN 1 THEN 'en_GB'
--      WHEN 7 THEN 'da_DK'
--      WHEN 8 THEN 'nl_NL'
      WHEN 9 THEN 'sv_SE'
    END AS locale,
    f.title,
    f.slug,
    f.content,
    f.settings,
    0
  FROM
    {$from_db}.osc_simple_cms_item_i18n AS f
  WHERE
    f.language_id = 9
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
    $record->id,
    $record->locale,
    mysql_real_escape_string($content)
  )) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
}

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
