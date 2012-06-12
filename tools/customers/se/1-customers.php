<?php

if (isset($argv[1]) && $argv[1] == 'live') {
  $from = 'pompdelux_se';
  $to = 'pdl_se';
  mysql_connect('192.168.2.137', 'pdl_se_migrate', 'TEMPMIGRATE111');
} else {
  $from = 'tmp_oscom_se';
  $to = 'tmp_hanzo_se';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

// cleanup first.
echo "[".date('Y-m-d H:i:s')."] truncating tables (customers, addresses, consultants)\n";
mysql_query("TRUNCATE TABLE {$to}.customers") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
mysql_query("TRUNCATE TABLE {$to}.addresses") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));
mysql_query("TRUNCATE TABLE {$to}.consultants") or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));


// migrate customers
echo "[".date('Y-m-d H:i:s')."] porting customers ....\n";
$query = "
  INSERT INTO {$to}.customers (
    id,
    first_name,
    last_name,
    password,
    password_clear,
    email,
    phone,
    discount,
    groups_id,
    is_active,
    created_at,
    updated_at
  )
  SELECT
    c.customers_id,
    c.customers_firstname,
    c.customers_lastname,
    SHA1(c.customers_password_cleartext),
    c.customers_password_cleartext,
    c.customers_email_address,
    c.customers_telephone,
    c.customers_discount,
    c.customers_groups_id,
    c.customers_status,
    i.customers_info_date_account_created,
    IF(i.customers_info_date_account_last_modified, i.customers_info_date_account_last_modified, i.customers_info_date_account_created)
  FROM
    {$from}.osc_customers AS c
    JOIN
      {$from}.osc_customers_info AS i
      ON
        (c.customers_id = i.customers_info_id)
  ORDER BY
    c.customers_id
  ON DUPLICATE KEY UPDATE
    first_name = c.customers_firstname,
    last_name = c.customers_lastname,
    password = SHA1(c.customers_password_cleartext),
    password_clear = c.customers_password_cleartext,
    email = c.customers_email_address,
    phone = c.customers_telephone
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

// migrate customer addresses
echo "[".date('Y-m-d H:i:s')."] porting addresses ....\n";
$query = "
  INSERT INTO {$to}.addresses (
    customers_id,
    type,
    first_name,
    last_name,
    address_line_1,
    address_line_2,
    postal_code,
    city,
    country,
    countries_id,
    state_province,
    company_name,
    latitude,
    longitude,
    created_at,
    updated_at
  )
  SELECT
    a.customers_id,
    CASE a.type
      WHEN 'company' THEN 'shipping'
      WHEN 'private' THEN 'payment'
      WHEN 'overnightbox' THEN 'overnightbox'
      ELSE  IF(a.entry_company <> '', 'shipping', 'payment')
    END AS type,
    a.entry_firstname,
    a.entry_lastname,
    a.entry_street_address,
    a.entry_suburb,
    a.entry_postcode,
    a.entry_city,
    (SELECT c.countries_name FROM {$from}.osc_countries AS c WHERE c.countries_id = a.entry_country_id) AS countries_name,
    (SELECT c.id FROM {$to}.countries AS c WHERE c.name = (SELECT oc.countries_name FROM {$from}.osc_countries AS oc WHERE oc.countries_id = a.entry_country_id)) AS countries_id,
    a.entry_state,
    a.entry_company,
    a.latitude,
    a.longitude,
    i.customers_info_date_account_created,
    IF(i.customers_info_date_account_last_modified, i.customers_info_date_account_last_modified, i.customers_info_date_account_created)
  FROM
    {$from}.osc_address_book AS a
    JOIN
      {$from}.osc_customers_info AS i
      ON
        (a.customers_id = i.customers_info_id)
    JOIN
      {$from}.osc_customers AS c1
      ON
        (c1.customers_id = a.customers_id)
  ORDER BY
    a.customers_id,
    a.address_book_id
  ON DUPLICATE KEY UPDATE
    postal_code = a.entry_postcode,
    first_name = a.entry_firstname,
    last_name = a.entry_lastname,
    address_line_1 = a.entry_street_address,
    address_line_2 = a.entry_suburb,
    postal_code = a.entry_postcode,
    city = a.entry_city,
    state_province = a.entry_state,
    company_name = a.entry_company
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));


// migrate consultants
echo "[".date('Y-m-d H:i:s')."] porting consultants ....\n";
$query = "
  INSERT INTO {$to}.consultants (
      id,
      initials,
      info,
      event_notes
    )
  SELECT
    c.customers_id,
    c.customers_initials,
    ci.notes,
    c.customers_notes
  FROM
    {$from}.osc_customers AS c
    LEFT JOIN
      {$from}.pdl_consultant_info AS ci
      ON
        (ci.consultant_id = c.customers_id)
  WHERE
    c.customers_groups_id = 2
  ON DUPLICATE KEY UPDATE
    initials = c.customers_initials,
    info = ci.notes,
    event_notes = c.customers_notes
";
mysql_query($query) or (die('Line: '.__LINE__."\n".mysql_error()."\n".$query));

mysql_query('SET FOREIGN_KEY_CHECKS = 1');
echo "[".date('Y-m-d H:i:s')."] - done -\n\n";
