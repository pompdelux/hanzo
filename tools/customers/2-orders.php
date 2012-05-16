<?php
mysql_connect('localhost', 'root', '');
mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

$from = 'tmp_oscom';
$to = 'tmp_hanzo';

// <code>

echo "[".date('Y-m-d H:i:s')."] \n";

// </code>

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
echo "- done -\n\n";
