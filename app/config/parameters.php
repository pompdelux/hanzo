<?php /* vim: set sw=4: */

/**
 * hf@bellcom.dk: We need to set some stuff across all servers, so we can't relay on static file
 * so we open an db connection and fetch need stuff.
 *
 * For currently avaliable vars, check: $container->getParameterBag()->all()
 */

$db_prefix = '';
$env = explode('_', $container->getParameter('kernel.environment'));

if (empty($env[1])) {
    $env[1] = $lang = 'dk';
} else {
    $lang = $env[1];
}

// prefix all other than dev
if ('dev' != $env[0]) {
    switch ($env[1]) {
        default:
            $db_prefix = $env[0].'_'.$lang.'_';
            break;
        case 'com':
            $db_prefix = $env[0].'_dk_';
            break;
    }
}

// ffs this is just not right !!!
$locale_map = [
    'at'  => [
        'locale'           => 'de_AT',
        'core.domain_key'  => 'AT',
        'core.language_id' => 8,
    ],
    'ch'  => [
        'locale'           => 'de_CH',
        'core.domain_key'  => 'AT',
        'core.language_id' => 9,
    ],
    'com' => [
        'locale'           => 'en_GB',
        'core.domain_key'  => 'COM',
        'core.language_id' => 2,
    ],
    'dk'  => [
        'locale'           => 'da_DK',
        'core.domain_key'  => 'DK',
        'core.language_id' => 1,
    ],
    'de'  => [
        'locale'           => 'de_DE',
        'core.domain_key'  => 'DE',
        'core.language_id' => 7,
    ],
    'fi'  => [
        'locale'           => 'fi_FI',
        'core.domain_key'  => 'FI',
        'core.language_id' => 6,
    ],
    'nl'  => [
        'locale'           => 'nl_NL',
        'core.domain_key'  => 'NL',
        'core.language_id' => 5,
    ],
    'no'  => [
        'locale'           => 'nb_NO',
        'core.domain_key'  => 'NO',
        'core.language_id' => 4,
    ],
    'se'  => [
        'locale'           => 'sv_SE',
        'core.domain_key'  => 'NO',
        'core.language_id' => 3,
    ],
];

foreach ($locale_map[$lang] as $key => $value) {
    if (isset($env[2]) && ('consultant' == $env[2]) && ('core.domain_key' == $key)) {
        $value = 'Sales'.$value;
    }

    $container->setParameter($key, $value);
}

// setting up asset version and base url, needed to support cdn
$container->setParameter('assets_version', 1);
$container->setParameter('assets_base_url', str_replace('http:', '', $container->getParameter('cdn')));

// workaround for travis-ci and not having a db connection on eg. install
try {
    $localDbConnection = new PDO(
        'mysql:host='.$container->getParameter($db_prefix.'database_host').';dbname='.$container->getParameter($db_prefix.'database_name'),
        $container->getParameter($db_prefix.'database_user'),
        $container->getParameter($db_prefix.'database_password'),
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
    );

    $stmt = $localDbConnection->prepare("SELECT * FROM settings WHERE ns = 'core'");
    $stmt->execute();
    $results = $stmt->fetchAll();
} catch (\Exception $e) {}

// Default settings
if (!empty($results)) {
    foreach ($results as $result) {
        $container->setParameter($result['c_key'], $result['c_value']);
    }
}

