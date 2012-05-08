<?php // æåå
mysql_connect('localhost', 'root', '');
mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');

mysql_query('SET FOREIGN_KEY_CHECKS = 0');

$sql = "
  SELECT
    ci.id,
    ci.locale,
    ci.settings,
    c.type
  FROM
    hanzo.cms_i18n AS ci
  JOIN
    hanzo.cms AS c
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
      $data = json_encode(array('category_ids' => $settings['category_id']), JSON_FORCE_OBJECT);
      break;

    case 'system':
      if (substr($record->settings, 0, 2) == 'a:') {
        $settings = unserialize($record->settings);
        $type = $settings['view'];

        switch($settings['view']) {
          case 'mannequin':
            $json = json_decode($settings['params']);
            if (!is_null($json)) {
              $data = json_encode($json, JSON_FORCE_OBJECT);
            }
            break 2;

          case 'category_search':
          case 'advanced_search':
            $settings['params'] = trim($settings['params']);
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
    $sql = "UPDATE hanzo.cms SET type = '".mysql_real_escape_string($type)."' WHERE id = {$record->id}";
    mysql_query($sql);
  }
  $sql = "UPDATE hanzo.cms_i18n SET settings = '".mysql_real_escape_string($data)."' WHERE id = {$record->id} AND locale = '{$record->locale}'";
  mysql_query($sql);
}


mysql_query('SET FOREIGN_KEY_CHECKS = 1');
