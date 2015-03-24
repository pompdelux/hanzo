<?php
header("Access-Control-Allow-Origin: *");

$uploadsDir = __DIR__.'/images/upload/'.date('mY').'/';
$filePrefix = uniqid();
$fileNames = [];
$errors = [];

foreach ($_FILES["pictures"]["error"] as $key => $error)
{
    if ($error == UPLOAD_ERR_OK)
    {
        if (!is_dir($uploadsDir))
        {
            mkdir($uploadsDir,0700, true);
        }

        $tmpName = $_FILES["pictures"]["tmp_name"][$key];
        $name     = $filePrefix.'_'. $_FILES["pictures"]["name"][$key];

        $fileNames[] = $name;

        move_uploaded_file($tmpName, "$uploadsDir/$name");
    }
}


// Send data back to symfony so it can send mails
$data = ['files' => $fileNames, 'data' => $_POST, 'errors' => $errors];
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
