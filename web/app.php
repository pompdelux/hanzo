<?php

// start parse time timer
$ts = microtime(1);

// let's send 204 headers for none existing images, javascripts and styles
$ignore = array('jpg', 'png', 'gif', 'js', 'css');
$ext = array_pop(explode('.', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
// dont use a 204 on old newsletter URL's.·
$isnewsletter = preg_match('/\/images\/nyhedsbrev\//', $_SERVER['REQUEST_URI']);
if (in_array($ext, $ignore) && !$isnewsletter) {
    header('HTTP/1.0 204 No Content', 204);
    exit;
}

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

use Symfony\Component\HttpFoundation\Request;
use Hanzo\Core\Tools;

$tdl = explode('.', $_SERVER['HTTP_HOST']);
$lang = '';
switch (array_pop($tdl)) {
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
    case 'com':
        if (!preg_match('/(da_DK|nb_NO|sv_SE|nl_NL|en_GB)/', $_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI'] != '/')) {
          $lang = '/en_GB/';
        }
        break;
}

if ($lang) {
  $goto = 'http://www.pompdelux.com'.str_replace('//', '/', $lang.str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']));
  header('Location: '.$goto , true, 301);
  exit;
}

$env = Tools::mapDomainToEnvironment();

$kernel = new AppKernel('prod_'.$env, false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);
$handle = $kernel->handle(Request::createFromGlobals());

header('X-hanzo-t: ' . (microtime(1) - $ts));
header('X-hanzo-m: ' . $kernel->getKernel()->humanReadableSize(memory_get_peak_usage()));

$kernel->getKernel()->terminate($handle);
