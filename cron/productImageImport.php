<?php /* vim: set sw=4: */

/**
 * Importing product images and maps the images to products.
 * Is delegated to all databases setup in the config file.
 *
 * @author un@bellcom.dk
 */


require __DIR__ . '/config.php';

$source_dir = __DIR__ . '/../web/images/products/import/';
$target_dir = __DIR__ . '/../web/images/products/';

$images_found = glob($source_dir.'*.{jpg,gif,png}',  GLOB_BRACE);
if (empty($images_found)) {
    exit;
}

/**
 * Find categories by product id's in target databases.
 *
 * @param \PDO   $connection      DB connection object.
 * @param string $connection_name Name of the connection, used for the internal cache.
 * @param int    $products_id     Product id.
 *
 * @return array
 */
function findProductCategories(PDO $connection, $connection_name, $products_id) {
    static $cache;

    if (empty($cache)) {
        $cache = [];
    }

    if (!empty($cache[$connection_name]) && isset($cache[$connection_name][$products_id])) {
        return $cache[$connection_name][$products_id];
    }

    if (empty($cache[$connection_name])) {
        $cache[$connection_name] = [];
    }
    if (empty($cache[$connection_name][$products_id])) {
        $cache[$connection_name][$products_id] = [];
    }

    /** @var PDOStatement $stmt */
    $stmt = $connection->prepare('
        SELECT 
            p2c.categories_id, 
            c.context 
        FROM  
            products_to_categories AS p2c 
        JOIN categories AS c ON (
                c.id = p2c.categories_id
            ) 
        WHERE 
            p2c.products_id = :products_id
            AND (
                c.context != "" 
                OR 
                c.context IS NOT NULL
            )
        ',
        array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)
    );

    $stmt->execute([
        ':products_id' => $products_id
    ]);

    foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $record) {
        $cache[$connection_name][$products_id][$record->categories_id] = $record->categories_id;
    }

    return $cache[$connection_name][$products_id];
}


$images = array();
foreach ($images_found as $file) {
    $image = basename($file);
    $srcmd5 = md5_file($source_dir . $image);
    $tgdmd5 = '';

    if (is_file($target_dir . $image)) {
        $tgdmd5 = md5_file($target_dir . $image);
    }

    // skip unchanged files
    if (($srcmd5 === $tgdmd5) || (false !== strpos($image, '('))) {
        #continue;
    }

    $images[$image] = $image;
}

if (0 === count($images)) {
    _dbug("no images to import, we stop here.");
    exit;
}
_dbug("importing ".count($images)." images");


// make sure the images are sorted
ksort($images, SORT_REGULAR);

// create index from sort.
$product_images = [];
$product_ids = [];
$product_categories_ids = [];
$product_categories_all_ids = [];
$failed = [];

// DK == vip
$pdo = $_databases['vip'];
$products_stmt      = $pdo->prepare('SELECT id FROM products WHERE sku = :master and master IS NULL', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$product_image_stmt = $pdo->prepare('SELECT id FROM products WHERE color = :color and master = :master', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

_dbug("finding images product and category reference.");
foreach ($images as $image) {

    // Luna-LS-Tshirt_Dark-Grey-Melange_overview_01.jpg
    // 9 is used as slash in color names, e.g. SnowSUITGIRLAW14_Navy9Rose_set_02.jpg
    // master         color             type     index
    @list($master, $color, $type, $index) = explode('_', pathinfo($image, PATHINFO_FILENAME));

    $color = str_replace(['9', '-'], ['/', ' '], $color);

    // See if any product with master and color even exists
    $product_image_stmt->execute([
        ':master' => $master,
        ':color' => $color
    ]);

    $product_image = $product_image_stmt->fetchColumn();

    if (empty($product_image) ||
        empty($index) ||
        preg_match('/(\(| |copy)/', $image)
    ){
        $failed[] = $image;
        _dbug("Error in picture: {$image}");
        continue;
    }

    if (empty($product_ids[$master])) {
        $products_stmt->execute([
            ':master' => $master
        ]);
        $products_id = $products_stmt->fetchColumn();

        if (empty($products_id)) {
            $failed[] = $image;
            continue;
        }

        $product_ids[$master] = $products_id;
    }

    $product_images[$master][] = $image;
}

$select_sql = '
    SELECT id
    FROM products_images
    WHERE products_id = :products_id
        AND image = :image
        AND color = :color
        AND type = :type
';
$select_slave_sql = '
    SELECT products_id
    FROM products_images
    WHERE id = :id
';
$product_sql = '
    INSERT INTO products_images
    SET products_id = :products_id,
        image = :image,
        color = :color,
        type = :type
';
$product_slave_sql = '
    INSERT INTO products_images
    SET id = :id,
        products_id = :products_id,
        image = :image,
        color = :color,
        type = :type
';
$category_select_sql = '
    SELECT sort
    FROM products_images_categories_sort
    WHERE products_id = :products_id
        AND categories_id = :categories_id
        AND products_images_id = :products_images_id
';
$category_sql = '
    INSERT INTO products_images_categories_sort
    SET products_id = :products_id,
        categories_id = :categories_id,
        products_images_id = :products_images_id,
        sort = 100
';

$image2id = [];
foreach ($_databases as $key => $conn) {
    // prepare statements for the different databases

    _dbug("using database: {$key}");

    if ($key == 'vip') {
        $product_stm = $conn->prepare($product_sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $select_stm = $conn->prepare($select_sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
    } else {
        $product_stm = $conn->prepare($product_slave_sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $select_stm = $conn->prepare($select_slave_sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
    }

    $category_select_stm = $conn->prepare($category_select_sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
    $category_stm = $conn->prepare($category_sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);

    if ($key == 'vip') {
        _dbug("finding master products");
        foreach ($product_images as $master => $images) {
            $products_id = $product_ids[$master];
            $category_ids = findProductCategories($conn, $key, $products_id);

            $product_categories_ids[$key][$products_id] = $category_ids;
            $product_categories_all_ids[$key][$products_id] = $category_ids;

            // loop images into db
            foreach($images as $image) {
                @list($master, $color, $type, $index) = explode('_', str_replace(['9', '-'], ['/', ' '], pathinfo($image, PATHINFO_FILENAME)));
                $index = (int) $index;

                $select_stm->execute([
                    ':products_id' => $products_id,
                    ':image' => $image,
                    ':color' => $color,
                    ':type' => $type,
                ]);
                $image_id = $select_stm->fetchColumn();

                // image does not exist, add it
                if (!$image_id) {
                    $product_stm->execute([
                        ':products_id' => $products_id,
                        ':image' => $image,
                        ':color' => $color,
                        ':type' => $type,
                    ]);
                    $image_id = $conn->lastInsertId();
                    $image2id[$image] = $image_id;
                } else {
                    $image2id[$image] = $image_id;
                }

                // add image to "products_images_to_categories"
                if (1 === $index) {
                    foreach ($category_ids as $category_id) {
                        $category_select_stm->execute([
                            ':products_id' => $products_id,
                            ':categories_id' => $category_id,
                            ':products_images_id' => $image_id,
                        ]);

                        // skip existing
                        if (0 < $category_select_stm->fetchColumn()) {
                            continue;
                        }

                        $category_stm->execute([
                            ':products_id' => $products_id,
                            ':categories_id' => $category_id,
                            ':products_images_id' => $image_id,
                        ]);
                    }
                }
            }
        }
    } else {
        _dbug("pushing info to 'slaves'");
        foreach ($product_images as $master => $images) {
            $products_id = $product_ids[$master];
            $category_ids = findProductCategories($conn, $key, $products_id);

            $product_categories_ids[$key][$products_id] = $category_ids;
            $product_categories_all_ids[$key][$products_id] = $category_ids;

            foreach($images as $image) {
                if (empty($image2id[$image])) {
                    continue;
                }

                @list($master, $color, $type, $index) = explode('_', str_replace(['9', '-'], ['/', ' '], pathinfo($image, PATHINFO_FILENAME)));
                $index = (int) $index;

                $image_id = $image2id[$image];

                $select_stm->execute([
                    ':id' => $image_id,
                ]);
                $check = $select_stm->fetchColumn();

                if (!$check) {
                    $product_stm->execute([
                        ':id' => $image_id,
                        ':products_id' => $products_id,
                        ':image' => $image,
                        ':color' => $color,
                        ':type' => $type,
                    ]);
                }

                // add image to "products_images_to_categories"
                if (1 === $index) {
                    foreach ($category_ids as $category_id) {
                        $category_select_stm->execute([
                            ':products_id' => $products_id,
                            ':categories_id' => $category_id,
                            ':products_images_id' => $image_id,
                        ]);

                        // skip existing
                        if (0 < $category_select_stm->fetchColumn()) {
                            continue;
                        }

                        $category_stm->execute([
                            ':products_id' => $products_id,
                            ':categories_id' => $category_id,
                            ':products_images_id' => $image_id,
                        ]);
                    }
                }
            }
        }
    }
}

// copy all images to the right location
_dbug("copying images to source dir.");
foreach ($product_images as $pid => $images) {
    foreach($images as $image) {
        copy($source_dir . $image, $target_dir . $image);
    }
}

// cleanup

// clear file cache to prevent errors.
clearstatcache();

$images_stmt = $pdo->prepare('SELECT i.id as id, i.image as image, i.color as color, p.sku as master FROM products_images i left JOIN products p on(p.id = i.products_id)', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$images_stmt->execute();

$product_stmt = $pdo->prepare('SELECT COUNT(id) FROM products WHERE color = :color and master = :master', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$image_records_to_delete = [];
while ($record = $images_stmt->fetchObject()) {
    // note we check for both old and new style image names.

    // If the file exists, or the lowercase file exists, do not delete anything
    if ((file_exists($source_dir.$record->image)) ||
        (file_exists($source_dir.strtolower($record->image)))
    ) {
        continue;
    }

    $image_records_to_delete[$record->id] = $record->image;

    // Find the master with correct color
    $product_stmt->execute([
        ':master' => $record->master,
        ':color' => $record->color
    ]);

    $image_master = $product_stmt->fetchColumn();
    if (!$image_master > 0) {
        $image_records_to_delete[$record->id] = $record->image;
    }
}

// cleanup
// remove images from unused categories
_dbug("delete unused image-to-category relations.");
foreach ($_databases as $key => $conn) {
    foreach ($product_categories_all_ids[$key] as $products_id => $categories) {
        $ids = implode(',', $categories);
        $conn->exec('DELETE FROM products_images_categories_sort WHERE products_id = '.$products_id.' AND categories_id NOT IN('.$ids.')');
    }
}

// remove images altogether
if (count($image_records_to_delete)) {
    _dbug("delete unused image-to-category relations.");
    foreach ($_databases as $key => $conn) {
        $ids = implode(',', array_keys($image_records_to_delete));
        $conn->exec('DELETE FROM products_images WHERE id IN('.$ids.')');
        $conn->exec('DELETE FROM products_images_categories_sort WHERE products_images_id IN('.$ids.')');
        $conn->exec('DELETE FROM products_images_product_references WHERE products_images_id IN('.$ids.')');
    }

    // Be sure that if the image should be deleted, it doesnt exist in products image dir
    // Otherwise it will never get deleted. I think.
    foreach ($image_records_to_delete as $image) {
        if (file_exists($target_dir.$image)) {
            @unlink($target_dir.$image);
        }
    }

    $txt = "Følgende produktbilleder er slettet fra databasen da de ikke længere var i filsystemet:\n\n";
    foreach ($image_records_to_delete as $k => $image) {
        $txt .= " - {$image}\n";
    }
    $txt .= "\n";
    _dbug($txt);

    mail(
        'it-drift@pompdelux.dk,pdl@bellcom.dk',
        'slettede billeder i billedeimporten',
        $txt,
        "Reply-To: it-drift@pompdelux.dk\r\nReturn-Path: it-drift@pompdelux.dk\r\nErrors-To: it-drift@pompdelux.dk\r\n",
        '-fit-drift@pompdelux.dk'
    );
}


if (count($failed)) {
    $txt = "Der er fejl i følgende produktbilleder:\n\n";
    foreach ($failed as $image) {
        $txt .= " - {$image}\n";
    }
    $txt .= "\nRet dem venligst\n";
    _dbug($txt);

    mail(
        'it-drift@pompdelux.dk,pdl@bellcom.dk',
        'fejl i billedeimporten',
        $txt,
        "Reply-To: it-drift@pompdelux.dk\r\nReturn-Path: it-drift@pompdelux.dk\r\nErrors-To: it-drift@pompdelux.dk\r\n",
        '-fit-drift@pompdelux.dk'
    );
}

// rescale all images
if (count($product_images)) {
    $tmp = [];
    foreach ($product_images as $master => $images) {
        foreach($images as $image) {
            $tmp[] = $image;
        }
    }
    $product_images = $tmp;
    unset($tmp);

    require __DIR__ . '/scaleProductImages.php';
}
_dbug("- done");
