<?php

if (isset($argv[1]) && $argv[1] == 'live') {

  $con = mysql_connect('192.168.2.118', 'pdl_dk_migrate', 'TEMPMIGRATE111');
  mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci', $con);
  mysql_query("ALTER TABLE pdl_dk.countries ADD ( `vat` DECIMAL(4,2) )") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
  mysql_query("UPDATE pdl_dk.countries SET vat = 25.00 WHERE name IN('Austria', 'Belgium', 'Bulgaria', 'Cyprus', 'Czech Republic', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Ireland', 'Italy', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'United Kingdom')", $con) or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

  $con = mysql_connect('192.168.2.136', 'pdl_no_migrate', 'TEMPMIGRATE111');
  mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci', $con);
  mysql_query("ALTER TABLE pdl_no.countries ADD ( `vat` DECIMAL(4,2) )") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
  mysql_query("UPDATE pdl_no.countries SET vat = 25.00 WHERE name IN('Austria', 'Belgium', 'Bulgaria', 'Cyprus', 'Czech Republic', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Ireland', 'Italy', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'United Kingdom')", $con) or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

  $con = mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
  mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci', $con);
  mysql_query("ALTER TABLE pdl_se.countries ADD ( `vat` DECIMAL(4,2) )") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
  mysql_query("UPDATE pdl_se.countries SET vat = 25.00 WHERE name IN('Austria', 'Belgium', 'Bulgaria', 'Cyprus', 'Czech Republic', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Ireland', 'Italy', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'United Kingdom')", $con) or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

} else {

  $con = mysql_connect('localhost', 'root', '');
  mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci', $con);
  mysql_query("ALTER TABLE hanzo.countries ADD ( `vat` DECIMAL(4,2) )");
  mysql_query("UPDATE hanzo.countries SET vat = 25.00 WHERE name IN('Austria', 'Belgium', 'Bulgaria', 'Cyprus', 'Czech Republic', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Ireland', 'Italy', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'United Kingdom')", $con);

}
