<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$dir               = 'images/upload/'.date('mY');
$uploadsDirWebPath = $dir;
$uploadsDir        = __DIR__.'/'.$dir;
$filePrefix = uniqid();
$fileNames  = [];
$errors     = [];

if (isset($_FILES) && !empty($_FILES))
{
    foreach ($_FILES["pictures"]["error"] as $key => $error)
    {
        switch ($error)
        {
            case UPLOAD_ERR_OK:
                if (!is_dir($uploadsDir))
                {
                    mkdir($uploadsDir,0700, true);
                }

                $tmpName = $_FILES["pictures"]["tmp_name"][$key];
                $name    = $filePrefix.'_'. $_FILES["pictures"]["name"][$key];
                $dest    = "$uploadsDir/$name";

                while (file_exists($dest))
                {
                    $filePrefix = uniqid();
                    $name       = $filePrefix.'_'. $_FILES["pictures"]["name"][$key];
                    $dest       = "$uploadsDir/$name";
                }

                $fileNames[] = $uploadsDirWebPath.'/'.$name;

                move_uploaded_file($tmpName, $dest);
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errors[] = ['type' => 'upload', 'value' => UPLOAD_ERR_CANT_WRITE ];
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors[] = ['type' => 'upload', 'value' => UPLOAD_ERR_NO_TMP_DIR ];
                break;
            case UPLOAD_ERR_NO_FILE:
                // Just ignore :)
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = ['type' => 'upload', 'value' => UPLOAD_ERR_PARTIAL ];
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = ['type' => 'upload', 'value' => UPLOAD_ERR_FORM_SIZE ];
                break;
            case UPLOAD_ERR_INI_SIZE:
                $errors[] = ['type' => 'upload', 'value' => UPLOAD_ERR_INI_SIZE ];
                break;
        }
    }
}

$verifiedData   = [];
$requiredFields = ['name', 'customer_number', 'order_number', 'product_info', 'description', 'contact'];

foreach ($requiredFields as $field)
{
    if (!isset($_POST[$field]))
    {
        $errors[] = ['type' => 'missing_field', 'value' => $field ];
        break;
    }

    // TODO: simple data validation
    $verifiedData[$field] = $_POST[$field];
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
