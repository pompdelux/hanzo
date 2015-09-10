<?php /* vim: set sw=4: */

/**
 * @author un@bellcom.dk
 */

require_once __DIR__ . '/config.php';

$source_dir = __DIR__ . '/../web/images/products/';
$target_dir = __DIR__ . '/../web/images/products/thumb/';

if (empty($product_images)) {
    $images_found = glob($source_dir.'*.{jpg,gif,png}',  GLOB_BRACE);
    if (empty($images_found)) {
        exit;
    }
} else {
    // product images - from image import
    $images_found = $product_images;
}

_dbug(date('Y-m-d H:i:s')." :: resizing ".count($images_found)." images: ", false);
$counter = 0;
foreach ($images_found as $file) {
    $image = basename($file);
    $source_image = $source_dir . $image;

    foreach ($_sizes as $k => $dimensions) {
        list ($w, $h) = explode('x', $dimensions);

        if (!is_file($source_image) || (empty($w) && empty($h))) {
          continue;
        }
        _dbug('.', false);

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
        // progressive jpeg
        if ('JPEG' == $im->getImageFormat()) {
            $im->setInterlaceScheme(Imagick::INTERLACE_PLANE);
        }
        $im->writeImage("{$target_dir}{$dimensions},{$image}");
        $im->destroy();
    }
    $counter++;
}
_dbug("\n", false);
_dbug("images resized to these presets: ".implode(', ', $_sizes));

mail(
    'it-drift@pompdelux.dk,pdl@bellcom.dk',
    'billedeimporten er nu færdig !',
    "Hej der,\n\nSå er produktbilledeimporten færdig for denne gang.\n\nover and out.\n",
    "Reply-To: it-drift@pompdelux.dk\r\nReturn-Path: it-drift@pompdelux.dk\r\nErrors-To: it-drift@pompdelux.dk\r\n",
    '-fit-drift@pompdelux.dk'
);
