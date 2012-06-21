<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $db = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $db = 'hanzo';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');

$update = "UPDATE {$db}.consultants SET info = '%s', event_notes = '%s' WHERE id = %d";

$result = mysql_query("SELECT id, info, event_notes FROM {$db}.consultants") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

while ($record = mysql_fetch_object($result)) {
  mysql_query(sprintf($update,
    mysql_real_escape_string($record->event_notes),
    mysql_real_escape_string($record->info),
    mysql_real_escape_string($record->id)
  )) or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
}
