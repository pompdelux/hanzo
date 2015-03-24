<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$uploadsDir = __DIR__.'/images/upload/'.date('mY').'/';
$filePrefix = uniqid();
$fileNames = [];
$errors = [];

xdebug_break();
if (isset($_FILES) && !empty($_FILES))
{
    foreach ($_FILES["pictures"]["error"] as $key => $error)
    {
        if ($error == UPLOAD_ERR_OK)
        {
            if (!is_dir($uploadsDir))
            {
                mkdir($uploadsDir,0700, true);
            }

            $tmpName = $_FILES["pictures"]["tmp_name"][$key];
            $name    = $filePrefix.'_'. $_FILES["pictures"]["name"][$key];
            $dest = "$uploadsDir/$name";

            while (file_exists($dest))
            {
                $filePrefix = uniqid();
                $name       = $filePrefix.'_'. $_FILES["pictures"]["name"][$key];
                $dest       = "$uploadsDir/$name";
            }

            $fileNames[] = $name;

            move_uploaded_file($tmpName, $dest);
        }
    }
}

$verifiedData = [];
$requiredFields = ['name', 'customer_number', 'order_number', 'product_info', 'description', 'contact'];

foreach ($requiredFields as $field)
{
    if (!isset($_POST[$field]))
    {
        $errors[] = ['type' => 'missing_field', 'value' => $field ];
        break;
    }

    // TODO: simple data validation
    $verifiedData[] = $_POST[$field];
}

// Send data back to symfony so it can send mails
$data = ['files' => $fileNames, 'data' => $verifiedData, 'errors' => $errors];
$url  = $_POST['callback_url'];

$content = json_encode( $data );

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-type: application/json"]);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);

die($json_response);
