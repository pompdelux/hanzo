<?php
/*
SET FOREIGN_KEY_CHECKS = 0;
  truncate table products;
  truncate table products_domains_prices;
  truncate table products_i18n;
  truncate table products_images;
  truncate table products_images_categories_sort;
  truncate table products_images_product_references;
  truncate table products_stock;
  truncate table products_to_categories;
  truncate table products_washing_instructions;
SET FOREIGN_KEY_CHECKS = 1;
*/

$from_db = 'pdl_dk';
$to_db = 'hanzo';

mysql_connect('localhost', 'root', '');
mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

mysql_query("TRUNCATE TABLE {$to_db}.products") or die('Line: '.__LINE__."\n".mysql_error());
mysql_query("TRUNCATE TABLE {$to_db}.products_i18n") or die('Line: '.__LINE__."\n".mysql_error());
mysql_query("TRUNCATE TABLE {$to_db}.products_stock") or die('Line: '.__LINE__."\n".mysql_error());
mysql_query("TRUNCATE TABLE {$to_db}.products_domains_prices") or die('Line: '.__LINE__."\n".mysql_error());
mysql_query("TRUNCATE TABLE {$to_db}.products_to_categories") or die('Line: '.__LINE__."\n".mysql_error());
mysql_query("TRUNCATE TABLE {$to_db}.products_images") or die('Line: '.__LINE__."\n".mysql_error());
mysql_query("TRUNCATE TABLE {$to_db}.products_images_categories_sort") or die('Line: '.__LINE__."\n".mysql_error());
mysql_query("TRUNCATE TABLE {$to_db}.products_washing_instructions") or die('Line: '.__LINE__."\n".mysql_error());


echo "- copying products\n"; flush();
// products
$query = "
  INSERT INTO
    {$to_db}.products (
      id,
      sku,
      master,
      size,
      color,
      unit,
      washing,
      created_at,
      updated_at
    )
  SELECT
    p.products_id,
    p.products_external_id,
    p.products_variant_id,
    p.products_attribute_1_value,
    p.products_attribute_2_value,
    p.products_attribute_3_value,
    IF((p.products_attribute_5_value != '' AND p.products_attribute_5_value != 0), p.products_attribute_5_value, NULL) AS washing,
    p.products_date_added,
    p.products_last_modified
  FROM
    {$from_db}.osc_products AS p
  WHERE
    p.products_external_id IS NOT NULL
  ORDER BY
    p.products_variant_id,
    p.products_external_id
";
mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");

echo "- copying products descriptions\n"; flush();
// descriptions
$query = "
  INSERT INTO
    {$to_db}.products_i18n
  SELECT
    p.products_id,
    CASE p.language_id
      WHEN 1 THEN 'en_EN'
      WHEN 7 THEN 'da_DK'
      WHEN 8 THEN 'nl_NL'
    END AS locale,
    p.products_name,
    p.products_description
  FROM
    {$from_db}.osc_products_description AS p
";
mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");

echo "- copying products stock\n"; flush();
// stock
$query = "
  INSERT INTO
    {$to_db}.products_stock (
      products_id,
      quantity,
      available_from
    )
  SELECT
    p.products_id,
    p.products_quantity,
    '2011-10-01'
  FROM
    {$from_db}.osc_products AS p
  WHERE
    p.products_external_id IS NOT NULL
";
mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");


echo "- copying products prices\n"; flush();
// prices
$query = "
  INSERT INTO
    {$to_db}.products_domains_prices (
      products_id,
      domains_id,
      price,
      vat,
      currency_id,
      from_date
  )
  SELECT
    p.products_id,
    CASE p.domain_id
      WHEN 1 THEN 1
      WHEN 3 THEN 5
      WHEN 7 THEN 4
    END AS domains_id,
    p.price,
    (p.price / 100 * 25) vat,
    CASE p.domain_id
      WHEN 1 THEN 208
      WHEN 3 THEN 978
      WHEN 7 THEN 978
    END AS currency,
    '2011-10-01 00:00:00'
  FROM
    {$from_db}.osc_products_to_domain AS p
  WHERE
    p.domain_id IN (1,3,7)
";
mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");

echo "- copying products discount prices\n"; flush();
// disount prices
$query = "
  INSERT INTO
    {$to_db}.products_domains_prices (
      products_id,
      domains_id,
      price,
      vat,
      currency_id,
      from_date,
      to_date
  )
  SELECT
    p.products_id,
    CASE p.domain_id
      WHEN 1 THEN 1
      WHEN 3 THEN 5
      WHEN 7 THEN 4
    END AS domains_id,
    p.discount_price,
    (p.discount_price / 100 * 25) vat,
    CASE p.domain_id
      WHEN 1 THEN 208
      WHEN 3 THEN 978
      WHEN 7 THEN 978
    END AS currency,
    p.discount_from_date,
    p.discount_to_date
  FROM
    {$from_db}.osc_products_to_domain AS p
  WHERE
    p.domain_id IN (1,3,7)
    AND
    p.discount_price IS NOT NULL
";
mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");

echo "- copying products to categories\n"; flush();
// products to categories
$query = "
  INSERT INTO
    {$to_db}.products_to_categories
  SELECT
    p.products_id,
    p.categories_id
  FROM
    {$from_db}.osc_products_to_categories as p
";
mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");


echo "- copying products images\n"; flush();
// product images
$query = "
  INSERT INTO
    {$to_db}.products_images
  SELECT
    NULL,
    p.products_id,
    p.products_image
  FROM
    {$from_db}.osc_products AS p
  WHERE
    p.products_external_id = p.products_variant_id
  AND
    p.products_image IS NOT NULL
";
mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");


echo "- copying products extra images\n"; flush();
// products extra images
$query = "
  SELECT
    p.products_id,
    p.products_image1,
    p.products_image2,
    p.products_image3,
    p.products_image4,
    p.products_image5,
    p.products_image6,
    p.products_image7
  FROM
    {$from_db}.osc_products_images AS p
";
$result = mysql_query($query);
while ($record = mysql_fetch_array($result)) {
  for($i=1; $i<8; $i++) {
    if (!empty($record['products_image' . $i])) {
      mysql_query("
        INSERT INTO
          {$to_db}.products_images (
            products_id,
            image
          )
        VALUES (
          " . $record['products_id'] . ",
          '" . mysql_real_escape_string(basename($record['products_image' . $i])) . "'
        )
      ");
    }
  }
}

echo "- copying products images to categories\n"; flush();
// product images to categories
$query = "
  SELECT
    products_id,
    categories_id
  FROM
    {$to_db}.products_to_categories
";
$result = mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");

while ($record = mysql_fetch_object($result)) {
  $query = "
    INSERT INTO
      {$to_db}.products_images_categories_sort
    SELECT
      " . $record->products_id . ",
      " . $record->categories_id . ",
      p.id,
      FLOOR(1 + (RAND() * 10))
    FROM
      {$to_db}.products_images AS p
    WHERE
      p.products_id = " . $record->products_id . "
  ";
  mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");
}

echo "- copying products washing instructions\n"; flush();
// product washing instructions
mysql_query("TRUNCATE TABLE {$to_db}.products_washing_instructions") OR die(mysql_error() . ' » ' . __LINE__ . "\n");
$query = "
  INSERT INTO
    {$to_db}.products_washing_instructions
  SELECT
    p.id,
    p.code,
    p.description,
    CASE p.languages_id
      WHEN 1 THEN 'en_EN'
      WHEN 7 THEN 'da_DK'
      WHEN 8 THEN 'nl_NL'
    END AS locale
  FROM
    {$from_db}.pdl_washing_instructions AS p
";
mysql_query($query) OR die(mysql_error() . ' » ' . __LINE__ . "\n");

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
