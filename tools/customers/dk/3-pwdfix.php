<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $db = 'pdl_dk';
  mysql_connect('192.168.2.118', 'pdl_dk_migrate', 'TEMPMIGRATE111');
} else {
  $db = 'hanzo';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');

$query = "SELECT id, password_clear FROM {$db}.customers";
$result = mysql_query($query);

echo "re encoding passwords:\n";
while ($record = mysql_fetch_object($result)) {
  echo "."; flush();

  $record->password_clear = utf8_decode($record->password_clear);

  mysql_query("
    UPDATE
      {$db}.customers
    SET
      password = '".sha1($record->password_clear)."'
    WHERE
      id = {$record->id}
  ");
}
echo "\n- done -\n";
