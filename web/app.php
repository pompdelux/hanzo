<?php
$ts = microtime(1);

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);
$kernel->handle(Request::createFromGlobals())->send();

/* <[ performance numbers ]> */

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
