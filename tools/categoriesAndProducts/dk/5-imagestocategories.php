<?php

if (!isset($argv[1])) {
	die('Pass string of category_ids as second argument');
}

if (isset($argv[2])) {
	$pdos = array(
		'dk' => new PDO('mysql:host=192.168.2.118;dbname=pdl_dk', 'pdl_dk', 'ceineeF8f3G',     array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
		'no'  => new PDO('mysql:host=192.168.2.136;dbname=pdl_no', 'pdl_no', 'Aivum9iud3Fs1',   array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
		'se'  => new PDO('mysql:host=192.168.2.137;dbname=pdl_se', 'pdl_se', 'cina5Weid4fZ',    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
		'nl'  => new PDO('mysql:host=192.168.2.217;dbname=pdl_nl', 'pdl_nl', 'soup8Ao7fGAS3',   array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
		'fi'  => new PDO('mysql:host=192.168.2.218;dbname=pdl_fi', 'pdl_fi', 'Gaigohg5sdfF2fg', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
		'test'  => new PDO('mysql:host=192.168.2.110;dbname=testpompdelux_dk', 'testpompdelux_dk', 'aid7Ief2', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
	);
	$pdo = $pdos[$argv[2]];

} else {
	$pdo = new PDO('mysql:host=localhost;dbname=hanzo', 'root', 'root', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

$products_images_sql = '
		SELECT i.products_id as product_id, i.id as image_id, c.categories_id FROM `products_images` i
		JOIN `products_to_categories` c ON (i.products_id = c.products_id)
		WHERE  `categories_id` IN ('.$argv[1].')
';

$insert_products_images_to_categories_sql = '
	INSERT INTO `products_images_categories_sort`(`products_id`, `categories_id`, `products_images_id`) VALUES (:products_id,:categories_id,:products_images_id)
';
$products_images_stmt = $pdo->prepare($products_images_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$category_stm = $pdo->prepare($insert_products_images_to_categories_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

$products_images_stmt->execute();
$result = $products_images_stmt->fetchAll(PDO::FETCH_OBJ);

foreach ($result as $record) {
	$category_stm->execute(array(
		':products_id' => $record->product_id,
		':categories_id' => $record->categories_id,
		':products_images_id' => $record->image_id,
	));
}

echo "All done. ".count($result)." images is added to the index\n";