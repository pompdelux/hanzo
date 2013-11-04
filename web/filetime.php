<?php
header('Content-type: text/javascript');

if (!empty($_GET['callback']) && !empty($_GET['data'])) {

    foreach ($_GET['data'] as $index => $file) {
        $url = parse_url($file['file']);
        $file = __DIR__ . $url['path'];

        if (is_file($file)) {
            $mtime = filemtime($file);
            $_GET['data'][$index]['mtime'] = date('d/m/Y H:i', $mtime);
            $_GET['data'][$index]['ts'] = $mtime;
        }
    }
}

die($_GET['callback'].'('.json_encode([
    'status' => true,
    'data'   => $_GET['data'],
]).');');
