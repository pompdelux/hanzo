<?php

require __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

$request = Request::createFromGlobals();

switch ($request->get('action')) {

    // get timestamp for countdown, used to sync time.
    case 'timestamp':
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Fri, 1 Jan 2010 00:00:00 GMT");
        header("Content-Type: text/plain; charset=utf-8");
        $now = new DateTime();
        echo $now->format("M j, Y H:i:s O")."\n";
        break;

    case 'apc-clear':
        $remote_ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];

        if (in_array($remote_ip, ['127.0.0.1', '::1'])) {
            apc_clear_cache();
            apc_clear_cache('user');
            apc_clear_cache('opcode');
            echo "APC Cache cleared by " . $_SERVER['REQUEST_URI'] . "\n";
        } else {
            echo "NOT clearing APC Cache. Run from a browser on the local server\n";
        }
        break;

    case 'opcode-clear':
        $remote_ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];

        if (in_array($remote_ip, ['127.0.0.1', '::1'])) {
            if (version_compare(phpversion(), '5.5', '>')) {
                opcache_reset();
                echo "OPcache cleared by " . $_SERVER['REQUEST_URI'] . "\n";
            } else {
                apc_clear_cache();
                apc_clear_cache('user');
                apc_clear_cache('opcode');
                echo "APC Cache cleared by " . $_SERVER['REQUEST_URI'] . "\n";
            }
        } else {
            echo "NOT clearing APC or OPcache. Run from a browser on the local server\n";
        }
        break;

    // handle file uploads to static
    case 'upload-files':
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json; charset=utf-8');

        $uploadsDirWebPath = 'images/upload/' . date('mY');
        $uploadsDir        = __DIR__ . '/' . $uploadsDirWebPath;

        $fileNames = [];
        $errors    = [];

        /** @var UploadedFile $upload */
        foreach ($request->files->get('images') as $upload) {
            if (!$upload instanceof UploadedFile) {
                continue;
            }

            if (UPLOAD_ERR_OK === $upload->getError()) {
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0700, true);
                }

                $name = uniqid() . '_' . $upload->getClientOriginalName();

                while (file_exists($uploadsDir . '/' . $name)) {
                    $name = uniqid() . '_' . $upload->getClientOriginalName();
                }

                $fileNames[] = $uploadsDirWebPath.'/'.$name;

                $upload->move($uploadsDir, $name);
                continue;
            }

            // if not ok, error ...
            $errors[] = [
                'type'  => 'upload',
                'value' => $upload->getError()
            ];
        }

        $verifiedData   = [];
        $requiredFields = [
            'name',
            'address',
            'zipcode',
            'city',
            'phone',
            'email',
            'describe_yourself',
            'describe_motivation',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                $errors[] = ['type' => 'missing_field', 'value' => $field ];
                break;
            }

            // TODO: simple data validation
            $verifiedData[$field] = $_POST[$field];

            if ($field == 'contact') {
                $name = $_POST[$field].'_value';
                $verifiedData['contact_value'] = $_POST[$name];
            }
        }

        // Send data back to Symfony so it can send mails
        $data = ['files' => $fileNames, 'data' => $verifiedData, 'errors' => $errors];
        $url  = $_POST['callback_url'];

        $guzzleClient = new \Guzzle\Http\Client();
        $response = $guzzleClient->post($url, ['Content-type' => 'application/json'], json_encode($data))->send();

        if ($response->isError()) {
            die(json_encode([
                'error' => true,
                'error_msg' => $response->getMessage(),
            ]));
        }
        die(json_encode($response->json()));

        break;
}
