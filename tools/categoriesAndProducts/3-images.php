<?php

$to_db = 'hanzo';
$from_db = 'hanzo_clone';

mysql_connect('localhost', 'root', '');
mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

mysql_query("TRUNCATE {$to_db}.products_images");
mysql_query("TRUNCATE {$to_db}.products_images_categories_sort");


$query = "SELECT products_id, image FROM {$from_db}.products_images ORDER BY image";
$result = mysql_query($query) or print(mysql_error());

$products = array();
while ($image = mysql_fetch_object($result)) {

  list($id, $junk) = explode('_', str_replace('-', ' ', $image->image));

  if (empty($products[$id])) {
    $product_result = mysql_query("SELECT id FROM {$to_db}.products WHERE sku = '".mysql_real_escape_string($id)."'") or print(mysql_error());
    if (mysql_num_rows($product_result)) {
      $product = mysql_fetch_object($product_result);

      $categories = array();
      $categories_result = mysql_query("SELECT categories_id FROM {$to_db}.products_to_categories WHERE products_id = " . $image->products_id) or print(mysql_error());
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

  mysql_query("INSERT INTO {$to_db}.products_images SET products_id = ".$product_id.", image = '".mysql_real_escape_string($image->image)."'") or print(mysql_error());
  $image_id = mysql_insert_id();

  foreach ($categories as $category_id) {
    mysql_query("INSERT INTO {$to_db}.products_images_categories_sort SET products_id = ".$product_id.", categories_id = " . $category_id . ", products_images_id = ".$image_id.", sort = ".rand(1, 10)) or print(mysql_error());
  }
}


mysql_query('SET FOREIGN_KEY_CHECKS = 1');
