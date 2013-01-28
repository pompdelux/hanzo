<?php

$info = pathinfo($_SERVER['REQUEST_URI']);
$ext = explode('?', strtolower($info['extension']));
$ext = array_shift($ext);

if ('robots.txt' == $info['basename']) {
    header('Content-type: text/plain');
?>
User-agent: *
Disallow: /images/xhjkpiydjns/
Disallow: /images/pdf/
Disallow: /images/mailsignatur/
<?php
  exit;
}

// do different stuff to different file extentions
switch ($ext)
{
  case 'jpg':
  case 'gif':
  case 'png':

    // get the path for the image
    $target = dirname(__FILE__) . $info['dirname'] . '/';
    $source = $target;

    if (strpos($target, 'thumb')) {
      $source = dirname($target) . '/';
    }

    $exp = explode('?', $info['basename']);
    list ($dimensions, $image) = explode(',', array_shift($exp));
    list ($w, $h) = explode('x', $dimensions);

    // send 404 if the file does not exist
    if (!is_file($source . $image) || (empty($w) && empty($h)))
    {
      header("HTTP/1.1 404 Not Found");
      die('404 - file not found');
    }

    $im = @getimagesize($source . $image);

    // keep aspect ratio
    if (empty($w) || empty($h))
    {
      $im = @getimagesize($source . $image);
      if (empty($w) && !empty($h))
      {
        $scaleFactor = $im[0] / $im[1];
        $w = ceil($h * $scaleFactor);
      }
      elseif (empty($h) && !empty($w))
      {
        $scaleFactor = $im[0] / $im[1];
        $h = ceil($w / $scaleFactor);
      }
    }

    // create the new image
    $im = new imagick($source . $image);
    $im->thumbnailImage($w, $h);
    $im->writeImage("{$target}{$dimensions},{$image}");

    // send header and image
    header ('HTTP/1.1 200 OK');
    header ("Content-Type: image/" . $im->getImageFormat());
    echo $im->getImageBlob ();

    break;
}
// send a 404 header for all other files
header("HTTP/1.1 404 Not Found");
die('404 - file not found');
