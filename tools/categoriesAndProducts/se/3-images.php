<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $to_db = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $to_db = 'tmp_hanzo_se';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

mysql_query("CREATE TEMPORARY TABLE {$to_db}.tmp_products_images SELECT * FROM {$to_db}.products_images") or (die('Line: '.__LINE__."\n".mysql_error()."\nCREATE TABLE {$to_db}.tmp_products_images SELECT * FROM {$to_db}.products_images"));

mysql_query("TRUNCATE {$to_db}.products_images");
mysql_query("TRUNCATE {$to_db}.products_images_categories_sort");

$query = "SELECT products_id, image FROM {$to_db}.tmp_products_images ORDER BY image";
$result = mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$products = array();

echo "Fixing image port ";

$used = array();
while ($image = mysql_fetch_object($result)) {
  echo '.';

  list($id, $junk) = explode('_', str_replace('-', ' ', $image->image));

  if (empty($products[$id])) {
    $product_result = mysql_query("SELECT id FROM {$to_db}.products WHERE sku = '".mysql_real_escape_string($id)."'") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
    if (mysql_num_rows($product_result)) {
      $product = mysql_fetch_object($product_result);

      $categories = array();
      $categories_result = mysql_query("SELECT categories_id FROM {$to_db}.products_to_categories WHERE products_id = " . $image->products_id) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
      while ($category = mysql_fetch_object($categories_result)) {
        $categories[] = $category->categories_id;
      }

      $products[$id] = array(
        'id' => $product->id,
        'categories' => $categories
      );
    } else {
      continue;
    }
  }

  $product_id = $products[$id]['id'];
  $categories = $products[$id]['categories'];

  if (!isset($used[$product_id.$image->image])) {
    $used[$product_id.$image->image] = $image->image;

    mysql_query("INSERT INTO {$to_db}.products_images SET products_id = ".$product_id.", image = '".mysql_real_escape_string($image->image)."'") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
    $image_id = mysql_insert_id();

    foreach ($categories as $category_id) {
      mysql_query("INSERT INTO {$to_db}.products_images_categories_sort SET products_id = ".$product_id.", categories_id = " . $category_id . ", products_images_id = ".$image_id.", sort = ".rand(1, 10)) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
    }
  }
}
echo "\n\n - done\n";

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
