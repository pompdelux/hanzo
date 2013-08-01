<?php
$source_dir = '/var/www/images/products/thumb/';

if (empty($_GET['name'])) {
    $images = glob($source_dir.'234x410,*_overview_01.jpg');

    $used = [];
    foreach ($images as $image) {
        $image = basename($image);
        preg_match('/,([^_.*]*)/', $image, $matches);
        list($name,) = explode('_over', $matches[1]);

        if (isset($used[$name])) { continue; }

        echo '"'.str_replace('-', ' ', $name).'","http://static.pompdelux.com/img.php?name='.$name.'"'."\r\n";
        $used[$name] = true;
    }

    exit;
}

$name = $_GET['name'];
$images = glob($source_dir.'234x410,'.$name.'*_overview_*.jpg');

?>
<!doctype html>
<html>
<head>
<title></title>
<style>body {margin:0; padding:0;} img {display:block; clear:both; margin:10px;}</style>
</head>
<body>
    <div>
<?php foreach ($images as $image): ?>
        <img src="http://static.pompdelux.com/images/products/thumb/<?php echo basename($image); ?>" alt="">
<?php endforeach; ?>
    </div>
</body>
</html>
