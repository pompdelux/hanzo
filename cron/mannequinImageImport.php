<?php /* vim: set sw=4: */

/**
 * Importing mannequin images and maps the images to products.
 * Is delegated to all databases setup in the config file.
 *
 * @author un@bellcom.dk
 */

require __DIR__ . '/config.php';

$source_dir = __DIR__ . '/../web/images/mannequin/import/';
$target_dir = __DIR__ . '/../web/images/mannequin/';

$failed = array();
$images = array();
$images_found = glob($source_dir.'*.png');

if (empty($images_found)) {
    exit;
}

$pdo = $_databases['vip'];

foreach ($_databases as $conn) {
    $conn->query('TRUNCATE TABLE mannequin_images');
}

$test_query = "
  SELECT
    id
  FROM
    products
  WHERE
    color = :color
    AND
      master = :master
";

$test_stm = $pdo->prepare($test_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

foreach($images_found as $image) {
    $image = basename($image);
    $md5_image = 'm'.md5($image).'.png';

    @list($master, $type, $color, $layer) = explode(',', basename($image, '.png'));

    $color = str_replace('-', '/', $color);
    $master = str_replace('-', ' ', $master);

    $test_stm->execute(array(
        ':color' => $color,
        ':master' => $master,
    ));
    $result = $test_stm->fetchAll(PDO::FETCH_COLUMN);

    if (0 === count($result)) {
        $failed[] = $image;
        continue;
    }

    switch ($type) {
        case 'l':
            $query = "
                INSERT INTO
                    mannequin_images
                SET
                    master = :master:,
                    color = :color:,
                    image = :md5_image:,
                    layer = :layer:
                ON DUPLICATE KEY UPDATE
                    image = :md5_image:,
                    layer = :layer:
            ";
            break;

        case 'i':
            $query = "
                INSERT INTO
                    mannequin_images
                SET
                    master = :master:,
                    color = :color:,
                    icon = :md5_image:
                ON DUPLICATE KEY UPDATE
                    icon = :md5_image:
            ";
            break;

        case 't':
            $query = "
                INSERT INTO
                    mannequin_images
                SET
                    master = :master:,
                    color = :color:,
                    icon = :md5_image:,
                    is_main = 1
                ON DUPLICATE KEY UPDATE
                    icon = :md5_image:,
                    is_main = 1
            ";
            break;
    }

    foreach ($_databases as $conn) {
        $conn->query(strtr($query, array(
            ':master:' => $conn->quote($master, PDO::PARAM_STR),
            ':color:' => $conn->quote($color, PDO::PARAM_STR),
            ':md5_image:' => $conn->quote($md5_image, PDO::PARAM_STR),
            ':layer:' => $conn->quote($layer, PDO::PARAM_INT),
        )));
    }

    copy($source_dir . $image, $target_dir . $md5_image);
}


if (count($failed)) {
    $txt = "Der er fejl i følgende produktbilleder:\n\n";
    foreach ($failed as $image) {
        $txt .= " - {$image}\n";
    }
    $txt .= "\nRet dem venligst!\n";

    mail(
        'it-drift@pompdelux.dk,pdl@bellcom.dk',
        'fejl i mannequin billedeimporten',
        $txt,
        "Reply-To: it-drift@pompdelux.dk\r\nReturn-Path: it-drift@pompdelux.dk\r\nErrors-To: it-drift@pompdelux.dk\r\n",
        '-fit-drift@pompdelux.dk'
    );
}
