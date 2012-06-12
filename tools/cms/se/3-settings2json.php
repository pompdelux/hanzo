<?php // æåå

if (isset($argv[1]) && $argv[1] == 'live') {
  $db_name = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $db_name = 'tmp_hanzo_se';
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

$query = "INSERT INTO {$db_name}.cms_thread_i18n (id, locale, title) VALUES (22, 'sv_SE', 'LÃ¸se sider')";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$query = "INSERT INTO {$db_name}.cms (id, parent_id, cms_thread_id, sort, type, is_active, created_at, updated_at) VALUES (NULL, NULL, '22', '1', 'frontpage', '1', '2012-05-31 00:00:00', NULL)";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$id = mysql_insert_id();

$query = "INSERT INTO {$db_name}.cms_i18n (id, locale, title, path, content, settings, is_restricted, old_path) VALUES
({$id}, 'sv_SE', 'Startsida', 'loese-sider/startsida', '<div>\r\n<a href=\"/forside/webshop\"><img src=\"/images/pages/Spring_sale_large2.jpg\"></a><br>\r\npÃ¥ SPRING/SUMMER12 kollektionen. BestÃ¤ll nu och fÃ¥ en gratis POMP BAG.<br><br>\r\n<h2>Om POMPdeLUX</h2>\r\nUnder 2006 startade vi POMPdeLUX, d&aring; vi saknade ett st&auml;lle d&auml;r man kunde k&ouml;pa kvalitetsbarnkl&auml;der utan att bli ruinerad. Vi hade b&aring;da tv&aring; arbetat med mode och design i m&aring;nga &aring;r, och efter en resa till Paris var id&egrave;n klar till ett helt nytt koncept: Vi skulle sj&auml;lva designa och producera kl&auml;derna, och f&ouml;r att h&aring;lla priserna nere skulle det inte s&auml;ljas i traditionella butiker utan p&aring; Home shopping arrangemang och i v&aring;r Webshop. <br><br>\r\n<h2>I Danmark &ndash; och utlandet</h2>\r\nUnder namnet POMPdeLUX har vi s&aring;lt v&aring;ra klassiska, skandinaviskt inspirerade kl&auml;der i hela Danmark. Under 2010 utvidgade vi oss till Norge och Sverige, och planen &auml;r att g&aring; in i fler l&auml;nder under kommande &aring;r. <br>\r\n<blockquote>Vi hade bÃ¥da tvÃ¥ arbetat med mode och design i mÃ¥nga Ã¥r, och efter en resa till Paris var idÃ¨n klar till ett helt nytt koncept.</blockquote>\r\n<h2>Unik design</h2>\r\nUt&ouml;ver kvalitetskravet har det hela tiden varit v&aring;rt m&aring;l att leverera en unik stil, som understryker barnens personlighet. D&auml;rf&ouml;r designar vi sj&auml;lva allt fr&aring;n knappar till tyger och print. Vi ser till att det &auml;r ett snitt och en stil som l&aring;ter barn vara barn, samtidigt som de kan vara l&auml;ckert p&aring;kl&auml;dda. <br><br>\r\n<h2>V&auml;lkommen till v&aring;rt POMPdeLUX universum</h2>\r\nMarianne Hoffmann och Pia Davids\r\n</div>\r\n<a href=\"/forside/om-pompdelux\" class=\"button\">LÃ¤s mer...</a>\r\n', NULL, 0, NULL)";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

mysql_query("UPDATE {$db_name}.cms_i18n SET settings = '{\"is_frontpage\":true}' WHERE id = 472");

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
