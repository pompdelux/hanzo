<?php /* vim: set sw=4: */

// note!
//  dk serves as "vip" database, so if this changes, this must be changed..

$_databases = array(
  // vip == dk
  'vip' => new PDO('mysql:host=localhost;dbname=hanzo', 'root', null, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
  'no' => new PDO('mysql:host=localhost;dbname=hanzo_slave', 'root', null, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
//  'se' => new PDO('mysql:host=localhost;dbname=hanzo', 'root', null, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
//  'nl' => new PDO('mysql:host=localhost;dbname=hanzo', 'root', null, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
//  'fi' => new PDO('mysql:host=localhost;dbname=hanzo', 'root', null, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
//  '..' => new PDO('mysql:host=localhost;dbname=hanzo', 'root', null, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
);
