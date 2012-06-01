<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $to_db = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $to_db = 'tmp_hanzo_se';
  mysql_connect('localhost', 'root', '');
}

mysql_select_db($to_db);

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');


class mpf {
  public static $lang = '';
  public static $menuItems;
  public static $map = array();

  protected static $cas = "
    SELECT
      cms.id,
      cms.parent_id,
      cms_i18n.locale,
      cms_thread_i18n.title AS thread,
      cms_i18n.title,
      cms_i18n.path
    FROM
      cms
    JOIN
      cms_i18n
        ON (cms.id = cms_i18n.id)
    JOIN
      cms_thread_i18n
        ON (
          cms.cms_thread_id = cms_thread_i18n.id
          AND
          cms_i18n.locale = cms_thread_i18n.locale
        )
    WHERE
      cms.parent_id :parent:
    ORDER BY
      cms.sort
  ";


  public static function t($pid = 'IS NULL')
  {
    $sql = strtr(self::$cas, array(':parent:' => $pid));

    $result = mysql_query($sql);

    while ($record = mysql_fetch_object($result)) {
      $prefix = self::stripText($record->thread);
      if ($record->parent_id && isset(self::$menuItems[$record->parent_id][$record->locale])) {
        $prefix = self::$menuItems[$record->parent_id][$record->locale]['path'];
      }

      $new = strtolower(trim($prefix . '/' . self::stripText($record->title), '/'));

      self::$menuItems[$record->id][$record->locale]['title'] = $record->title;
      self::$menuItems[$record->id][$record->locale]['path'] = $new;

      self::t('= ' . $record->id);
    }
  }

  public static function stripText($v)
  {
    $url_safe_char_map = array(
      'æ' => 'ae', 'Æ' => 'AE',
      'ø' => 'oe', 'Ø' => 'OE',
      'å' => 'aa', 'Å' => 'AA',
      'é' => 'e',  'É' => 'E', 'è' => 'e', 'È' => 'E',
      'à' => 'a',  'À' => 'A', 'ä' => 'a', 'Ä' => 'A', 'ã' => 'a', 'Ã' => 'A',
      'ò' => 'o',  'Ò' => 'O', 'ö' => 'o', 'Ö' => 'O', 'õ' => 'o', 'Õ' => 'O',
      'ù' => 'u',  'Ù' => 'U', 'ú' => 'u', 'Ú' => 'U', 'ũ' => 'u', 'Ũ' => 'U',
      'ì' => 'i',  'Ì' => 'I', 'í' => 'i', 'Í' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I',
      'ß' => 'ss',
      'ý' => 'y', 'Ý' => 'Y',
      ' ' => '-',
      '/' => '-',
    );

    $search  = array_keys($url_safe_char_map);
    $replace = array_values($url_safe_char_map);

    $v = str_replace(' ', '-', trim($v));
    $v = str_replace($search, $replace, $v);

    $v = preg_replace('/[^a-z0-9_-]+/i', '', $v);
    $v = preg_replace('/[-]+/', '-', $v);
    $v = preg_replace('/^-|-$/', '-', $v);

    return strtolower($v);
  }

  public static function set() {
    $query = "
      UPDATE
        cms_i18n
      SET
        path = ':path:'
      WHERE
        id = :id:
        AND
          locale = ':locale:'
    ";

    foreach(self::$menuItems as $id => $item) {
      foreach($item as $locale => $data) {
        mysql_query(strtr($query, array(
          ':path:' => $data['path'],
          ':id:' => $id,
          ':locale:' => $locale
        )));
      }
    }
  }
}

mpf::t();
mpf::set();
