<?php // æåå

if (isset($argv[1]) && $argv[1] == 'live') {
  $from_db = 'pdlfront_bellcom_dk';
  $to_db = 'pdl_dk';
  mysql_connect('192.168.2.118', 'pdl_dk_migrate', 'TEMPMIGRATE111');
} else {
  $from_db = 'pdl_dk';
  $to_db = 'hanzo';
  mysql_connect('localhost', 'root', '');
}

mysql_query('SET NAMES utf8 COLLATE utf8_unicode_ci');
mysql_query('SET FOREIGN_KEY_CHECKS = 0');

echo "[".date('Y-m-d H:i:s')."] cleaning tables\n"; flush();
mysql_query("TRUNCATE TABLE {$to_db}.events") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));
mysql_query("TRUNCATE TABLE {$to_db}.events_participants") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

echo "[".date('Y-m-d H:i:s')."] migrating event data\n"; flush();
mysql_query("
  INSERT INTO
    {$to_db}.events (
      id,
      code,
      `key`,
      consultants_id,
      customers_id,
      event_date,
      host,
      address_line_1,
      postal_code,
      city,
      phone,
      email,
      description,
      type,
      is_open,
      notify_hostess,
      created_at,
      updated_at
    )
  SELECT
    id,
    event_code,
    IF(login_code IS NULL, MD5(RAND()), login_code),
    consultant_id,
    customers_id,
    event_date,
    hostess,
    address,
    zipcode,
    town,
    phone,
    email,
    description,
    event_type,
    event_open,
    hostess_mail,
    created_at,
    created_at
  FROM
    {$from_db}.pdl_events
") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));

echo "[".date('Y-m-d H:i:s')."] migrating event participants\n"; flush();

mysql_query("
  INSERT INTO
    {$to_db}.events_participants (
      id,
      events_id,
      `key`,
      invited_by,
      first_name,
      last_name,
      email,
      phone,
      tell_a_friend,
      notify_by_sms,
      sms_send_at,
      has_accepted,
      expires_at
    )
  SELECT
    id,
    event_id,
    login_code,
    invited_by,
    firstname,
    lastname,
    email,
    mobile_phone,
    tell_a_friend,
    sms,
    sms_date,
    accepted,
    expire_date
  FROM
   {$from_db}.pdl_event_participant
") or (die('Line: '.__LINE__."\n".mysql_error()."\n"));


echo "[".date('Y-m-d H:i:s')."] done\n"; flush();
mysql_query('SET FOREIGN_KEY_CHECKS = 1');
