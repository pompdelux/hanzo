<?php

switch ((empty($_GET['action']) ? '' : $_GET['action'])) {

    // get timestamp for countdown, used to sync time.
    case 'timestamp':
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Fri, 1 Jan 2010 00:00:00 GMT");
        header("Content-Type: text/plain; charset=utf-8");
        $now = new DateTime();
        echo $now->format("M j, Y H:i:s O")."\n";
        break;

}
