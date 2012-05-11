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

$images = array();
foreach ($images_found as $file)
{
    $image = basename($file);
    $images[$image] = $image;
}

// make sure the images are sorted
ksort($images, SORT_REGULAR);

// create index from sort.
$product_images = array();
$extra_images = array();
$product_ids = array();
$product_categories_ids = array();

$pdo = $_databases['vip'];
$products_stmt = $pdo->prepare('SELECT id FROM products WHERE sku = :master and master IS NULL', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$categories_stmt = $pdo->prepare('SELECT categories_id FROM products_to_categories WHERE products_id = :products_id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

foreach ($images as $image)
{
    // ex: key = PANTYHOSE-2-PACK_01.jpg
    @list($master, $junk) = explode('_', str_replace('-', ' ', $image));

    if (!preg_match('/^[0-9]+/', $junk)) {
        $extra_images[$master][] = $image;
        continue;
    }

    if (empty($product_ids[$master])) {
        $products_stmt->execute(array(
            ':master' => $master
        ));
        $products_id = $products_stmt->fetchColumn();

        if (empty($products_id)) {
            continue;
        }

        $product_ids[$master] = $products_id;

        // fetch categories for the product
        $categories_stmt->execute(array(
            ':products_id' => $products_id
        ));
        foreach ($categories_stmt->fetchAll(PDO::FETCH_OBJ) as $record) {
            $product_categories_ids[$products_id][$record->categories_id] = $record->categories_id;
        }

    }

    $product_images[$master][] = $image;
}


$select_sql = '
    SELECT id
    FROM products_images
    WHERE products_id = :products_id
    AND image = :image
';
$product_sql = '
    INSERT INTO products_images
    SET products_id = :products_id,
        image = :image
';
$category_sql = '
    INSERT INTO products_images_categories_sort
    SET products_id = :products_id,
        categories_id = :categories_id,
        products_images_id = :products_images_id,
        sort = 100
';

foreach ($_databases as $key => $conn) {
    // prepare statements for the different databases
    $product_stm = $conn->prepare($product_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $select_stm = $conn->prepare($select_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $category_stm = $conn->prepare($category_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

    foreach ($product_images as $master => $images) {
        $products_id = $product_ids[$master];

        // loop images into db
        foreach($images as $image) {
            $select_stm->execute(array(
                ':products_id' => $products_id,
                ':image' => $image,
            ));
            $image_id = $select_stm->fetchColumn();

            // image does not exist, add it
            if (!$image_id) {
                $product_stm->execute(array(
                    ':products_id' => $products_id,
                    ':image' => $image,
                ));
                $image_id = $conn->lastInsertId();

                // add image to "products_images_to_categories"
                foreach ($product_categories_ids[$products_id] as $category_id) {
                    $category_stm->execute(array(
                        ':products_id' => $products_id,
                        ':categories_id' => $category_id,
                        ':products_images_id' => $image_id,
                    ));
                }
            }
        }

        // "clean up" you say ...
        // no - we do not clean up, but should we ?
    }
}

// copy all images to the right location
foreach (array_merge($product_images, $extra_images) as $pid => $images) {
    foreach($images as $image) {
        copy($source_dir . $image, $target_dir . $image);
    }
}
