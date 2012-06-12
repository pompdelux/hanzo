<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $to_db = 'pdl_no';
  mysql_connect('192.168.2.136', 'pdl_no_migrate', 'TEMPMIGRATE111');
} else {
  $to_db = 'hanzo';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');

// replace hd tags

$find = array(
  '/templates/pompdelux/images/',
  'SIMPLE_NEWSLETTER_FORM',
  'TEXT_EXPECTED_DELIVERY_DATE',
  'HD_COSTUMERSERVICE_CONTACT_INFO',
  'PDL_GEO_CONSULTANTS_ZIP hus',
  'PDL_GEO_CONSULTANTS hus',
  'PDL_GEO_CONSULTANTS_ZIP',
  'PDL_GEO_CONSULTANTS',
  'CONSULTANT_MATRIX',
  'PDL_GMAP',
);
$replace = array(
  '/fx/images/',
  " embed('newsletter_form', {'view':'simple'}) ",
  " parameter('expected_delivery_date') ",
  " 'customer.service.contact.info'|trans|raw ",
  ' geo_zip_code_form("hus") ',
  ' consultants_near_you() ',
  ' geo_zip_code_form() ',
  ' consultants_near_you() ',
  ' consultants_map() ',
  ' consultants_map() ',
);

$query = "SELECT id, locale, content FROM {$to_db}.cms_i18n";
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
