<?php

// start parse time timer
$ts = microtime(1);

// if you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it, or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '90.185.183.84', // Bellcom office
    '85.236.67.1',
    '87.104.21.83', // Ulrik home
    '192.168.42.52', // Ulrik local
    '87.104.49.70', // Henrik home
    '192.168.0.4', // Henrik local
    '127.0.0.1',
    '::1',
))) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$handle = $kernel->handle(Request::createFromGlobals());

header('X-hanzo-t: ' . (microtime(1) - $ts));
header('X-hanzo-m: ' . $kernel::hrSize(memory_get_peak_usage()));

$handle->send();
