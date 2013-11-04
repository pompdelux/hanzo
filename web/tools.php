<?php

require __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

switch ($request->query->get('action')) {

    // get timestamp for countdown, used to sync time.
    case 'timestamp':
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Fri, 1 Jan 2010 00:00:00 GMT");
        header("Content-Type: text/plain; charset=utf-8");
        $now = new DateTime();
        echo $now->format("M j, Y H:i:s O")."\n";
        break;


    case 'apc-clear':
        $remote_ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];

        if (in_array($remote_ip, array('127.0.0.1', '::1'))) {
            apc_clear_cache();
            apc_clear_cache('user');
            apc_clear_cache('opcode');
            echo "APC Cache cleared by " . $_SERVER['REQUEST_URI'] . "\n";
        } else {
            echo "NOT clearing APC Cache. Run from a browser on the local server\n";
        }
}
