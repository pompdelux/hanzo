<?php

// start parse time timer
$ts = microtime(1);

// let's send 204 headers for none existing images, javascripts and styles
$ignore = array('jpg', 'png', 'gif', 'js', 'css');
$ext = array_pop(explode('.', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
if (in_array($ext, $ignore)) {
  header('HTTP/1.0 204 No Content', 204);
  exit;
}

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);
$handle = $kernel->handle(Request::createFromGlobals());

header('X-hanzo-t: ' . (microtime(1) - $ts));
header('X-hanzo-m: ' . $kernel->getKernel()->hrSize(memory_get_peak_usage()));

$handle->send();
