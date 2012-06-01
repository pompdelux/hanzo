<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $db_name = 'pdl_dk';
  mysql_connect('192.168.2.118', 'pdl_dk_migrate', 'TEMPMIGRATE111');
} else {
  $db_name = 'tmp_hanzo_dk';
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

$query = "INSERT INTO {$db_name}.cms_thread_i18n (id, locale, title) VALUES (22, 'da_DK', 'LÃ¸se sider'), (22, 'en_GB', 'Misc pages'), (22, 'nl_NL', 'Misc pages')";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$query = "INSERT INTO {$db_name}.cms (id, parent_id, cms_thread_id, sort, type, is_active, created_at, updated_at) VALUES (NULL, NULL, '22', '1', 'frontpage', '1', '2012-05-31 00:00:00', NULL)";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$id = mysql_insert_id();

$query = "INSERT INTO {$db_name}.cms_i18n (id, locale, title, path, content, settings, is_restricted, old_path) VALUES
({$id}, 'da_DK', 'Forsiden', 'loese-sider/forsiden', '<div>\r\n    <h2>Danmarks Indsamlingen</h2>\r\n    Overskuddet fra salget af Berte LS LONG TSHIRT gÃ¥r ubeskÃ¥ret til Danmarks Indsamlingen 2012, hvor temaet er \"BÃ¸rn pÃ¥ flugt\". <br>\r\n    Ved Danmarks Indsamlingen 2011 gav vi 250.000 til indsamlingen \" sÃ¥ hjÃ¦lp os med at hjÃ¦lpe andre \"?? og fÃ¥ en lÃ¦kker TSHIRT. <br><br>\r\n    Den skÃ¸nne fugl pÃ¥ Berte LS LONG TSHIRT er tegnet af Alberte pÃ¥ 10 Ã¥r fra Horsens. <br>\r\n    <a href=\"/forside/webshop/girl-104-152-cm/t-shirts/153/berte-ls-long-tshirt\">Â» Berte LS LONG TSHIRT</a>\r\n    <h2>Om POMPdeLUX</h2>\r\n    I 2006 startede vi POMPdeLUX, da vi virkelig savnede et sted, hvor man kunne k&oslash;be kvalitets-b&oslash;rnet&oslash;j uden at blive ruineret. Vi havde hver is&aelig;r arbejdet med mode og design gennem flere &aring;r, og efter en tur til Paris l&aring; id&eacute;en klar til et helt nyt koncept: Vi ville selv designe og producere t&oslash;jet, og for at holde prisen nede skulle det ikke s&aelig;lges i traditionelle butikker men via Home shopping arrangementer og i vores Webshop. <br><br>\r\n    <h2>I Danmark &ndash; og udlandet</h2>\r\n    Under navnet POMPdeLUX har vi lige siden solgt vores klassiske, skandinavisk inspirerede tÃ¸j over hele Danmark. I 2010 udvidede vi til Norge og Sverige, og efter planen kommer endnu flere lande med i de kommende Ã¥r.<br>\r\n    <br>\r\n    <h2>Unikt design</h2>\r\n    Ud over kvalitetskravet har det hele tiden v&aelig;ret vores m&aring;l, at levere en unik stil, som understreger b&oslash;rnenes personlighed. Derfor designer vi selv alt fra knapper til stof og print. Vi s&oslash;rger for et snit og en stil, der lader b&oslash;rn v&aelig;re b&oslash;rn, samtidig med at de er l&aelig;kkert kl&aelig;dt p&aring;. <br><br>\r\n    <h2>Velkommen i vores POMPdeLUX univers</h2>\r\n    Marianne Hoffmann og Pia Davids\r\n</div>\r\n<a href=\"/forside/om-pompdelux\" class=\"button\">LÃ¦s mere...</a>\r\n', NULL, 0, NULL),
({$id}, 'en_GB', 'Front page', 'misc-pages/front-page', '<div>\r\n    <h2>Danmarks Indsamlingen</h2>\r\n    Overskuddet fra salget af Berte LS LONG TSHIRT gÃ¥r ubeskÃ¥ret til Danmarks Indsamlingen 2012, hvor temaet er \"BÃ¸rn pÃ¥ flugt\". <br>\r\n    Ved Danmarks Indsamlingen 2011 gav vi 250.000 til indsamlingen â?? sÃ¥ hjÃ¦lp os med at hjÃ¦lpe andre â?? og fÃ¥ en lÃ¦kker TSHIRT. <br><br>\r\n    Den skÃ¸nne fugl pÃ¥ Berte LS LONG TSHIRT er tegnet af Alberte pÃ¥ 10 Ã¥r fra Horsens. <br>\r\n    <a href=\"/forside/webshop/girl-104-152-cm/t-shirts/153/berte-ls-long-tshirt\">Â» Berte LS LONG TSHIRT</a>\r\n    <h2>Om POMPdeLUX</h2>\r\n    I 2006 startede vi POMPdeLUX, da vi virkelig savnede et sted, hvor man kunne k&oslash;be kvalitets-b&oslash;rnet&oslash;j uden at blive ruineret. Vi havde hver is&aelig;r arbejdet med mode og design gennem flere &aring;r, og efter en tur til Paris l&aring; id&eacute;en klar til et helt nyt koncept: Vi ville selv designe og producere t&oslash;jet, og for at holde prisen nede skulle det ikke s&aelig;lges i traditionelle butikker men via Home shopping arrangementer og i vores Webshop. <br><br>\r\n    <h2>I Danmark &ndash; og udlandet</h2>\r\n    Under navnet POMPdeLUX har vi lige siden solgt vores klassiske, skandinavisk inspirerede tÃ¸j over hele Danmark. I 2010 udvidede vi til Norge og Sverige, og efter planen kommer endnu flere lande med i de kommende Ã¥r.<br>\r\n    <br>\r\n    <h2>Unikt design</h2>\r\n    Ud over kvalitetskravet har det hele tiden v&aelig;ret vores m&aring;l, at levere en unik stil, som understreger b&oslash;rnenes personlighed. Derfor designer vi selv alt fra knapper til stof og print. Vi s&oslash;rger for et snit og en stil, der lader b&oslash;rn v&aelig;re b&oslash;rn, samtidig med at de er l&aelig;kkert kl&aelig;dt p&aring;. <br><br>\r\n    <h2>Velkommen i vores POMPdeLUX univers</h2>\r\n    Marianne Hoffmann og Pia Davids\r\n</div>\r\n<a href=\"/forside/om-pompdelux\" class=\"button\">LÃ¦s mere...</a>\r\n', NULL, 0, NULL),
({$id}, 'nl_NL', 'Voorpagina', 'misc-pages/voorpagina', '<div>\r\n<h2>Over POMPdeLUX</h2>\r\nIn 2006 werd POMPdeLUX opgestart omdat we vonden dat er een winkel ontbrak waar je  goede kinderkleding kon kopen zonder dat je daardoor meteen geruÃ¯neerd werd. We hadden ieder voor zich jarenlang met mode en design gewerkt en na een reis naar Parijs hadden we een idee voor een nieuw concept: we wilden zelf kleding ontwerpen en produceren en om de prijzen laag te houden, zouden we die niet in de winkels verkopen, maar in onze eigen Webshop.<br><br>\r\n<h2>In Denemarken â€“ en het buitenland</h2>\r\nSindsdien verkopen we onze klassieke, Scandinavische kleding onder de naam POMPdeLUX in heel Denemarken. In 2010 openden we in Noorwegen en Zweden en als alles volgens plan verloopt, zullen er de komende jaren nog meer landen bijkomen.<br><br>\r\n<h2>Uniek design</h2>\r\nNaast de kwaliteitseisen die we voortdurend stellen, is het ons doel om een unieke stijl te creÃ«ren die de persoonlijkheid van je kind onderstreept. Daarom ontwerpen we alles zelf: van de knopen tot de stof en de print. We zorgen voor een snit en een stijl die kinderen een optimale bewegingsvrijheid biedt, terwijl ze er leuk uitzien.<br><br>\r\n<h2>Welkom in de wereld van POMPdeLUX</h2>\r\nMarianne Hoffmann en Pia Davids\r\n</div>\r\n<a href=\"/voorpagina/over-pompdelux\" class=\"button\">Lees meer...</a>\r\n', NULL, 0, NULL)";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

mysql_query("UPDATE {$db_name}.cms_i18n SET settings = '{\"is_frontpage\":true}' WHERE id = 472");

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
