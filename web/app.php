<?php

// start parse time timer
$ts = microtime(1);

// let's send 404 headers for non existing images, javascripts and styles
$ignore = array('jpg', 'png', 'gif', 'js', 'css');
$ext    = explode('.', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$ext    = array_pop($ext);

if (in_array($ext, $ignore)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Hanzo\Core\Tools;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
Tools::handleRobots();

// temporary redirects because of switch from cc-tld to .com. Remove when all old links are updated
$tdl = explode('.', $_SERVER['HTTP_HOST']);
$lang = '';
switch (array_pop($tdl)) {
    case 'de':
        $lang = '/de_DE/';
        break;
    case 'dk':
        $lang = '/da_DK/';
        break;
    case 'no':
        $lang = '/nb_NO/';
        break;
    case 'se':
        $lang = '/sv_SE/';
        break;
    case 'nl':
        $lang = '/nl_NL/';
        break;
    case 'fi':
        $lang = '/fi_FI/';
        break;
    case 'at':
        $lang = '/de_AT/';
        break;
    case 'ch':
        $lang = '/de_CH/';
        break;
    case 'com':
        if (!preg_match('/(de_DE|da_DK|nb_NO|sv_SE|nl_NL|fi_FI|de_AT|de_CH|en_GB)/', $_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI'] != '/')) {
            $lang = '/en_GB/';
        }
        break;
}

if ($lang) {
    //error_log('app.php redirecting to: '.$lang);
    $goto = 'http://www.pompdelux.com'.str_replace('//', '/', $lang.str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']));
    header('Location: '.$goto , true, 301);
    exit;
}

$env = Tools::mapDomainToEnvironment();

if (in_array(@$_SERVER['REMOTE_ADDR'], array('::1', '127.0.0.1'))) {
    $dev = true;
    $env = 'dev_'.$env;
} else {
    $dev = false;
    $env = 'prod_'.$env;

    $loader = new ApcClassLoader('sf2', $loader);
    $loader->register(true);
}

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel($env, $dev);
$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->headers->set('X-hanzo-t', (microtime(1) - $ts));
$response->headers->set('X-hanzo-m', Tools::humanReadableSize(memory_get_peak_usage()));
$response->send();
$kernel->terminate($request, $response);
