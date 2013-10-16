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
            $db_prefix = $env[0].'_dk_';
            break;
        case 'se':
            $db_prefix = $env[0].'_se_';
            break;
        case 'no':
            $db_prefix = $env[0].'_no_';
            break;
        case 'de':
            $db_prefix = $env[0].'_de_';
            break;
      // case 'nl':
      //   $db_prefix = $env[0].'_nl_';
      //   break;
    }
}

// ffs this is just not right !!!
$locale_map = [
    'com' => 'en_GB',
    'dk'  => 'da_DK',
    'de'  => 'de_DE',
    'fi'  => 'fi_FI',
    'nl'  => 'nl_NL',
    'no'  => 'nb_NO',
    'se'  => 'sv_SE',
    'at'  => 'de_AT',
    'ch'  => 'de_CH',
];
$container->setParameter('locale', $locale_map[$lang]);

$dbUser     = $container->getParameter($db_prefix.'database_user');
$dbPassword = $container->getParameter($db_prefix.'database_password');
$dbName     = $container->getParameter($db_prefix.'database_name');
$dbHost     = $container->getParameter($db_prefix.'database_host');

$localDbConnection = new PDO( 'mysql:host='. $dbHost .';dbname='. $dbName , $dbUser, $dbPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
$stmt              = $localDbConnection->prepare( "SELECT * FROM settings WHERE ns = 'core'" );
$stmt->execute();
$results           = $stmt->fetchAll();

// setting up assetic version and baseurl, needed to support cdn
$container->setParameter('assets_version', 1);
$container->setParameter('assets_base_url', str_replace('http:', '', $container->getParameter('cdn')));

// Default settings
if ( !empty($results) ) {
    foreach ($results as $result) {
        $container->setParameter($result['c_key'], $result['c_value']);
    }
}

