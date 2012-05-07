<?php /* vim: set sw=4: */

/**
 * hf@bellcom.dk: We need to set some stuff across all servers, so we can't relay on static file
 * so we open an db connection and fetch need stuff.
 *
 * For currently avaliable vars, check: $container->getParameterBag()->all()
 */

$dbUser     = $container->getParameter('database_user');
$dbPassword = $container->getParameter('database_password');
$dbName     = $container->getParameter('database_name');
$dbHost     = $container->getParameter('database_host');

$localDbConnection = new PDO( 'mysql:host='. $dbHost .';dbname='. $dbName , $dbUser, $dbPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
$stmt              = $localDbConnection->prepare( "SELECT * FROM settings WHERE ns = 'core'" );
$stmt->execute();
$results           = $stmt->fetchAll();

// Default settings
$container->setParameter('assets_version', 1);

if ( !empty($results) )
{
    foreach ($results as $result) 
    {
        $container->setParameter($result['c_key'], $result['c_value']);
    }
}
