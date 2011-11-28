<?php

exit;

mysql_connect('localhost', 'root', '');
mysql_select_db('pdl_test');

$xml = simplexml_load_file(__DIR__ . '/dl_iso_table_a1.xml');

foreach ($xml->ISO_CURRENCY as $c) {

  $res = mysql_query("SELECT id FROM countries WHERE name = '{$c->ENTITY}'");
  if ($res && $record = mysql_fetch_object($res)) {
    mysql_query("
      UPDATE
        countries
      SET
        currency_id = '{$c->NUMERIC_CODE}',
        curency_code = '{$c->ALPHABETIC_CODE}',
        curerncy_name = '" . $c->CURRENCY . "'
      WHERE
        id = {$record->id}
    ");
  }
}
