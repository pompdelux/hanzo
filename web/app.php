<?php

// let's send 204 headers for none existing images, javascripts and styles
$ignore = array('jpg', 'png', 'gif', 'js', 'css');
$ext = array_pop(explode('.', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
if (in_array($ext, $ignore)) {
  header('HTTP/1.0 204 No Content', 204);
  exit;
}

// start parse time timer
$ts = microtime(1);

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);
$kernel->handle(Request::createFromGlobals())->send();


/**
 * only send performance numbers if not a json responce
 * <[ performance numbers ]>
 */

if (!defined('JSON_RESPONCE')) {

function _c($size){
    $unit = array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

echo "
<!--
  t: " . (microtime(1) - $ts). '
  m: ' . _c(memory_get_peak_usage()) . '
-->
';
}
