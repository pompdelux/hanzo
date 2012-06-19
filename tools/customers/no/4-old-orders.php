<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $from = 'pompdelux_no';
  $to = 'pdl_no';
  mysql_connect('192.168.2.136', 'pdl_no_migrate', 'TEMPMIGRATE111');
} else {
  $from = 'tmp_oscom_no';
  $to = 'tmp_hanzo_no';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');
// <code>

echo "[".date('Y-m-d H:i:s')."] migrating orders (start).\n";

$query = "
  INSERT INTO {$to}.orders (
    id,
    session_id,
    payment_gateway_id,
    state,
    customers_id,
    first_name,
    last_name,
    email,
    phone,
    languages_id,
    billing_first_name,
    billing_last_name,
    billing_company_name,
    billing_address_line_1,
    billing_address_line_2,
    billing_postal_code,
    billing_city,
    billing_country,
    billing_countries_id,
    billing_state_province,
    billing_method,
    delivery_first_name,
    delivery_last_name,
    delivery_company_name,
    delivery_address_line_1,
    delivery_address_line_2,
    delivery_postal_code,
    delivery_city,
    delivery_country,
    delivery_countries_id,
    delivery_state_province,
    delivery_method,
    finished_at,
    created_at,
    updated_at,
    in_edit,
    version_id,
    currency_code
  )
  SELECT
    o.orders_id,
    o.orders_id,
    IF(o.payment_gateway_id = 0, NULL, o.payment_gateway_id),
    CASE o.orders_status
      WHEN 1 THEN 30
      WHEN 2 THEN -50
      WHEN 3 THEN 40
      WHEN 4 THEN 50
    END as state,
    o.customers_id,
    o.customers_name,
    null,
    o.customers_email_address,
    o.customers_telephone,
    CASE o.domain_name
      WHEN 'pompdelux.dk' THEN 1
      WHEN 'konsulent.pompdelux.dk' THEN 1
      WHEN 'pompdelux.com' THEN 2
      WHEN 'pompdelux.se' THEN 3
      WHEN 'konsulent.pompdelux.se' THEN 3
      WHEN 'pompdelux.no' THEN 4
      WHEN 'konsulent.pompdelux.no' THEN 4
      WHEN 'pompdelux.nl' THEN 5
        ELSE 1
    END AS language_id,
    o.billing_name,
    null,
    o.billing_company,
    o.billing_street_address,
    o.billing_suburb,
    o.billing_postcode,
    o.billing_city,
    o.billing_country,
    (SELECT c.id FROM {$to}.countries AS c WHERE c.name = o.billing_country),
    o.billing_state,
    o.payment_method,
    o.delivery_name,
    null,
    o.delivery_company,
    o.delivery_street_address,
    o.delivery_suburb,
    o.delivery_postcode,
    o.delivery_city,
    o.delivery_country,
    (SELECT c.id FROM {$to}.countries AS c WHERE c.name = o.delivery_country),
    o.delivery_state,
    null,
    o.orders_date_finished,
    o.date_purchased,
    o.last_modified,
    0,
    1,
    o.currency
  FROM
    {$from}.osc_orders AS o
  WHERE
    o.orders_status = 4
  ORDER BY
    o.orders_id DESC
  ON DUPLICATE KEY UPDATE
    version_id = version_id + 1
";
#mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

echo "[".date('Y-m-d H:i:s')."] migrating orders (end).\n";
echo "[".date('Y-m-d H:i:s')."] migrating order lines (start).\n";

$query = "
  INSERT INTO {$to}.orders_lines (
    orders_id,
    type,
    products_id,
    products_sku,
    products_name,
    products_color,
    products_size,
    expected_at,
    original_price,
    price,
    vat,
    quantity,
    unit
  )
  SELECT
    p.orders_id,
    'product',
    p.products_id,
    IF(p.products_model = '', CONCAT(p.products_name, ' ', p.products_id), CONCAT(p.products_name, ' ', p.products_model)),
    p.products_name,
    (SELECT x.products_attribute_2_value FROM {$from}.osc_products AS x WHERE x.products_id = p.products_id),
    (SELECT x.products_attribute_1_value FROM {$from}.osc_products AS x WHERE x.products_id = p.products_id),
    p.products_expected,
    p.products_price,
    p.final_price,
    p.products_tax,
    p.products_quantity,
    (SELECT x.products_attribute_3_value FROM {$from}.osc_products AS x WHERE x.products_id = p.products_id)
  FROM {$from}.osc_orders_products AS p
  INNER JOIN
    {$from}.osc_orders AS o
    ON
      (p.orders_id = o.orders_id)
  WHERE
    o.orders_status = 4
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

echo "[".date('Y-m-d H:i:s')."] migrating order lines (end).\n";
echo "[".date('Y-m-d H:i:s')."] migrating order attributes (start).\n";

$query = "
  SELECT
    orders_id,
    cc_type,
    cc_number,
    cc_transactionid
  FROM
    {$from}.osc_orders
  WHERE
    orders_status = 4
  ORDER BY
    orders_id
";
$result = mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

$attributes_sql = "
  INSERT INTO
    {$to}.orders_attributes
  SET
    orders_id = %d,
    ns = '%s',
    c_key = '%s',
    c_value = '%s'
";

$lines_sql = "
  INSERT INTO
    {$to}.orders_lines
  SET
    orders_id = %d,
    type = '%s',
    products_sku = '%s',
    products_name = '%s',
    price = %f,
    original_price = %f,
    vat = %f,
    quantity = 1,
    expected_at = NULL
";

while ($record = mysql_fetch_object($result)) {

  $orders_id = $record->orders_id;

  // cc_type, cc_number, cc_transactionid
  $sql = "
    INSERT INTO
      {$to}.orders_attributes (
        orders_id,
        ns,
        c_key,
        c_value
      )
    VALUES (
      {$orders_id},
      'payment',
      'card_type',
      '{$record->cc_type}'
    ),(
      {$orders_id},
      'payment',
      'card_number',
      '{$record->cc_number}'
    ),(
      {$orders_id},
      'payment',
      'transact',
      '{$record->cc_transactionid}'
    )
  ";
  mysql_query($sql) or die('Line: '.__LINE__."\n".mysql_error());

  $res = mysql_query("SELECT * FROM {$from}.osc_orders_attributes WHERE orders_id = {$orders_id}");

  $orders_attributes = array();
  while ($r = mysql_fetch_object($res)) {
    $orders_attributes[$r->attribute_key] = $r->attribute_value;
  }

  // attatchments
  foreach ($orders_attributes as $key => $value) {
    if(substr($key, 0, 12) == 'attatchment_') {
      mysql_query(sprintf($attributes_sql, $orders_id, 'attachment', $key, $value)) or die('Line: '.__LINE__."\n".mysql_error());
    }


    if (in_array($key, array('event_id', 'event_type', 'hostessDiscount', 'consultants_id'))) {
      if ($value) {
        mysql_query(sprintf($attributes_sql, $orders_id, 'event', $key, $value)) or die('Line: '.__LINE__."\n".mysql_error());
      }
    }
  }

  if (isset($orders_attributes['delivery_date']) && $orders_attributes['delivery_date']) {
    mysql_query(sprintf($attributes_sql, $orders_id, 'global', 'delivery_date', $orders_attributes['delivery_date'])) or die('Line: '.__LINE__."\n".mysql_error());
  }

  if (isset($orders_attributes['domain_id']) && $orders_attributes['domain_id']) {
    mysql_query(sprintf($attributes_sql, $orders_id, 'global', 'domain_key', $orders_attributes['domain_id'])) or die('Line: '.__LINE__."\n".mysql_error());
  }

  // handling
  if (isset($orders_attributes['handling_cost_raw'])) {
    mysql_query(sprintf($attributes_sql, $orders_id, 'handling', 'cost_raw', $orders_attributes['handling_cost_raw']));
    if (isset($orders_attributes['handling_cost_raw']) && $orders_attributes['handling_cost_raw_title']) {
      mysql_query(sprintf($attributes_sql, $orders_id, 'handling', 'cost_raw_title', $orders_attributes['handling_cost_raw_title'])) or die('Line: '.__LINE__."\n".mysql_error());
    }

    if (isset($orders_attributes['shipping_cost_raw_title'])) {
        mysql_query(sprintf($lines_sql,
          $orders_id,
          'shipping.fee',
          90,
          $orders_attributes['shipping_cost_raw_title'],
          $orders_attributes['handling_cost_raw'],
          $orders_attributes['handling_cost_raw'],
          0
        )) or die('Line: '.__LINE__."\n".mysql_error());
      }
  }

  // shipping
  if (isset($orders_attributes['shipping_cost_raw_title']) && isset($orders_attributes['shipping_cost_raw'])) {
    mysql_query(sprintf($lines_sql,
      $orders_id,
      'shipping',
      10,
      $orders_attributes['shipping_cost_raw_title'],
      $orders_attributes['shipping_cost_raw'],
      $orders_attributes['shipping_cost_raw'],
      0
    )) or die('Line: '.__LINE__."\n".mysql_error());
  }

  // hostess discount
  if (isset($orders_attributes['voucher_text']) && $orders_attributes['voucher_text'] && isset($orders_attributes['voucher_value']) && $orders_attributes['voucher_value']) {
    mysql_query(sprintf($lines_sql,
      $orders_id,
      'discount',
      'hostess_discount',
      'hostess.discount',
      ($orders_attributes['voucher_value'] * -1),
      ($orders_attributes['voucher_value'] * -1),
      0
    )) or die('Line: '.__LINE__."\n".mysql_error());
  }
}

echo "[".date('Y-m-d H:i:s')."] migrating order attributes (end).\n";

// </code>

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
echo "\n- done -\n\n";
