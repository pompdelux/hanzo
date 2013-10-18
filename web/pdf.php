<?php
/**
 * trigger download of invoices and credit notes.
 *·
 * @author ulrik nielsen <ulrik@bellcom.dk>
 */

if (empty($_SERVER['HTTP_REFERER']) || empty($_GET['key']) || empty($_GET['file']) || empty($_GET['folder'])) { die(); }

$file = $_GET['file'];
$key  = $_GET['key'];
$folder  = $_GET['folder'];

$path = dirname(dirname(__FILE__)) . '/pdfupload/';

if (substr($_SERVER['HTTP_HOST'], 0, 6) == 'static') {
  $path = '/var/www/pompdelux/shared/web/pdfupload/';
}

$path .= str_replace('_', '/', $folder). '/';

// so far we only support pdf files...
if (preg_match('/[a-z]{2}_[0-9a-z]+_[0-9]+\.pdf/i', $file) && is_file($path.$file))
{
    header("Expires: 0");
    header("Cache-control: private");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Description: File Transfer");
    header("Content-Type: application/pdf");
    header("Content-disposition: attachment; filename=" . $file);
    readfile($path.$file);
    exit;
}

header('Location: ' . $_SERVER['HTTP_REFERER'], true);
