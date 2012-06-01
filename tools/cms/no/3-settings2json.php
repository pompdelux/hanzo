<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $db_name = 'pdl_no';
  mysql_connect('192.168.2.136', 'pdl_no_migrate', 'TEMPMIGRATE111');
} else {
  $db_name = 'tmp_hanzo_no';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

$sql = "
  SELECT
    ci.id,
    ci.locale,
    ci.settings,
    c.type
  FROM
    {$db_name}.cms_i18n AS ci
  JOIN
    {$db_name}.cms AS c
    ON
      (c.id = ci.id)
  WHERE
    ci.settings IS NOT NULL
  ORDER BY
    ci.settings
";
$result = mysql_query($sql);

while ($record = mysql_fetch_object($result)) {
  $record->settings = stripslashes($record->settings);

  if ('a:' != substr($record->settings, 0, 2)) {
    continue;
  }

  $data = '';
  $type = '';

  switch($record->type) {
    case 'page':
    case 'url':
      $settings = unserialize($record->settings);
      if (!empty($settings['params'])) {
        $json = json_decode($settings['params']);
        if (!is_null($json)) {
          $data = json_encode($json, JSON_FORCE_OBJECT);
        }
      }
      break;

    case 'category':
      $settings = unserialize($record->settings);
      $data = json_encode(array('category_id' => $settings['category_id']), JSON_FORCE_OBJECT);
      break;

    case 'system':
      if (substr($record->settings, 0, 2) == 'a:') {
        $settings = unserialize($record->settings);
        $type = $settings['view'];

        switch($settings['view']) {
          case 'mannequin':
            $json = json_decode($settings['params']);
            if (!is_null($json)) {
              $data = json_encode(array(
                'category_ids' => $json->categories,
                'image' => $json->image,
                'title' => $json->title,
                'colorscheme' => $json->colorscheme,
                'ignore' => $json->ignore,
              ), JSON_FORCE_OBJECT);
            }
            break 2;

          case 'category_search':
          case 'advanced_search':
            $type = 'search';
            if ($settings['params']) {
              list($category_ids, $group) = explode('/', $settings['params'], 2);
              $data = json_encode(array(
                'type' => 'category',
                'group' => $group,
                'category_ids' => $category_ids
              ), JSON_FORCE_OBJECT);
            } else {
              $data = json_encode(array('type' => 'advanced'), JSON_FORCE_OBJECT);
            }
            break 2;
        }
      }
      break;

    default:
      print_r($record->type);
      echo "\n";
  }

  if ($type) {
    $sql = "UPDATE {$db_name}.cms SET type = '".mysql_real_escape_string($type)."' WHERE id = {$record->id}";
    mysql_query($sql);
  }

  $settings = 'NULL';
  if ($data) {
    $settings = "'".mysql_real_escape_string($data)."'";
  }
  $sql = "UPDATE {$db_name}.cms_i18n SET settings = {$settings} WHERE id = {$record->id} AND locale = '{$record->locale}'";
  mysql_query($sql);
}

// extras:
$query = "INSERT INTO {$db_name}.cms_thread (id, is_active) VALUES ('22' ,  '1')";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$query = "INSERT INTO {$db_name}.cms_thread_i18n (id, locale, title) VALUES (22, 'nb_NO', 'LÃ¸se sider'), (22, 'en_GB', 'Misc pages'), (22, 'nl_NL', 'Misc pages')";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$query = "INSERT INTO {$db_name}.cms (id, parent_id, cms_thread_id, sort, type, is_active, created_at, updated_at) VALUES (NULL, NULL, '22', '1', 'frontpage', '1', '2012-05-31 00:00:00', NULL)";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$id = mysql_insert_id();

$query = "INSERT INTO {$db_name}.cms_i18n (id, locale, title, path, content, settings, is_restricted, old_path) VALUES
({$id}, 'nb_NO', 'Forsiden', 'loese-sider/forsiden', '<div>\r\n<a href=\"/forside/webshop\"><img src=\"/images/pages/Spring_sale_large2.jpg\"></a><br>\r\npÃ¥ SPRING/SUMMER12 kolleksjonen. Bestill nÃ¥ og fÃ¥ en gratis POMP BAG. <br>\r\n<br>\r\n<h2>Om POMPdeLUX</h2>\r\nI 2006 startet vi POMPdeLUX, da vi virkelig savnet et sted hvor man kunne kjÃ¸pe kvalitetsbarneklÃ¦r uten Ã¥ bli ruinert. Vi hadde begge jobbet med mote og design gjennom flere Ã¥r, og etter en tur til Paris lÃ¥ ideen klar til et helt nytt konsept: Vi ville selv designe og produsere klÃ¦rne, og for Ã¥ holde prisen nede, skulle det ikke selges i tradisjonelle butikker, men via Home shopping arrangementer og i vÃ¥r Webshop.\r\n<br>\r\n<br>\r\n<h2>I Danmark â€“ og utlandet</h2>\r\nUnder navnet POMPdeLUX har vi solgt vÃ¥re klassiske, skandinavisk inspirerte klÃ¦r over hele Danmark. I 2010 utvidet vi til Norge og Sverige, og etter planen utvider vi til enda flere land de kommende Ã¥r. <br>\r\n<blockquote>Vi hadde begge jobbet med mote og design gjennom flere Ã¥r, og etter en tur til Paris lÃ¥ ideen klar til et helt nytt konsept.</blockquote>\r\n<h2>Unik design</h2>\r\nI tillegg til kvalitetskravet har det hele tiden vÃ¦ret vÃ¥rt mÃ¥l Ã¥ levere en unik stil som understreker barnas personlighet. Derfor designer vi selv alt fra knapper til stoff og trykk. Vi sÃ¸rger for et snitt og en stil som lar barn vÃ¦re barn, samtidig med at de gÃ¥r i lekre klÃ¦r. <br>\r\n<br>\r\n<h2>Velkommen til vÃ¥rt POMPdeLUX-univers</h2>\r\nMarianne Hoffmann og Pia Davids </div>\r\n<a href=\"/forside/om-pompdelux\" class=\"button\">Les mer...</a>\r\n', NULL, 0, NULL)";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

mysql_query("UPDATE {$db_name}.cms_i18n SET settings = '{\"is_frontpage\":true}' WHERE id = 472");

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
