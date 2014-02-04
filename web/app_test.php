<?php

// start parse time timer
$ts = microtime(1);

// let's send 404 headers for none existing images, javascripts and styles
$ignore = array('jpg', 'png', 'gif', 'js', 'css');
$ext = explode('.', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$ext = array_pop($ext);
// dont use a 404 on old newsletter URL's, but pass it to the symfony 404 handler that will redirect. Get rid of this hack when noone reads old newsletters anymore
$isnewsletter = preg_match('/\/images\/nyhedsbrev\//', $_SERVER['REQUEST_URI']);
if (in_array($ext, $ignore) && !$isnewsletter) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Hanzo\Core\Tools;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
Tools::handleRobots();

$env = Tools::mapDomainToEnvironment();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('test_'.$env, false);
$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->headers->set('X-hanzo-t', (microtime(1) - $ts));
$response->headers->set('X-hanzo-m', Tools::humanReadableSize(memory_get_peak_usage()));
$response->send();
$kernel->terminate($request, $response);
