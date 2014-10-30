<?php /* vim: set sw=4: */

/**
 * hf@bellcom.dk: We need to set some stuff across all servers, so we can't relay on static file
 * so we open an db connection and fetch need stuff.
 *
 * For currently avaliable vars, check: $container->getParameterBag()->all()
 */

$dbPrefix = '';
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
            $dbPrefix = $env[0].'_'.$lang.'_';
            break;
        case 'com':
            $dbPrefix = $env[0].'_dk_';
            break;
    }
}

// ffs this is just not right !!!
$locale_map = \Hanzo\Core\Tools::getDomainLocaleMaps();

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
        'mysql:host='.$container->getParameter($dbPrefix.'database_host').';dbname='.$container->getParameter($dbPrefix.'database_name'),
        $container->getParameter($dbPrefix.'database_user'),
        $container->getParameter($dbPrefix.'database_password'),
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
    );

    $stmt = $localDbConnection->prepare("SELECT * FROM settings WHERE ns = 'core'");
    $stmt->execute();
    $results = $stmt->fetchAll();
} catch (\Exception $e) {}

// Default settings
if (!empty($results)) {
    foreach ($results as $result) {

        $prefix = 'core.';
        if ('assets_version' === $result['c_key']) {
            $prefix = '';
        }
        $container->setParameter($prefix.$result['c_key'], $result['c_value']);
    }
}

