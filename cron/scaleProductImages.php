<?php /* vim: set sw=4: */

/**
 * @author un@bellcom.dk
 */

require_once __DIR__ . '/config.php';

$source_dir = __DIR__ . '/../web/images/products/';
$target_dir = __DIR__ . '/../web/images/products/thumb/';

$images_found = glob($source_dir.'*.{jpg,gif,png}',  GLOB_BRACE);
if (empty($images_found)) {
    exit;
}

_dbug("resizing images: ", false);
$counter = 0;
foreach ($images_found as $file) {
    $image = basename($file);
    $source_image = $source_dir . $image;

    foreach ($_sizes as $k => $dimensions) {
        list ($w, $h) = explode('x', $dimensions);

        if (!is_file($source_image) || (empty($w) && empty($h))) {
          continue;
        }
        if ($_debug) { echo "."; }

        $im = @getimagesize($source_image);

        // keep aspect ratio
        if (empty($w) || empty($h)) {
          $im = @getimagesize($source_image);
          if (empty($w) && !empty($h)) {
            $scaleFactor = $im[0] / $im[1];
            $w = ceil($h * $scaleFactor);
          } elseif (empty($h) && !empty($w)) {
            $scaleFactor = $im[0] / $im[1];
            $h = ceil($w / $scaleFactor);
          }
        }

        // create the new image
        $im = new imagick($source_image);
        $im->thumbnailImage($w, $h);
        $im->writeImage("{$target_dir}{$dimensions},{$image}");
        $im->destroy();
    }
    $counter++;
}
if ($_debug) { echo "\n {$counter} images resized to these sizes: ".implode(', ', $_sizes)."\n"; }
