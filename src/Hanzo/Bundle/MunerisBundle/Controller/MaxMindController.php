<?php

namespace Hanzo\Bundle\MunerisBundle\Controller;

use Hanzo\Core\CoreController;
use Guzzle\Http\Exception\ClientErrorResponseException;

class MaxMindController extends CoreController
{
    public function lookupAction($ip = '')
    {
        $start = (microtime(true) * 1000);
        $service = $this->get('muneris.maxmind');

        try {
            $result = $service->lookup($ip);
        } catch (ClientErrorResponseException $e) {
            return $this->json_response([
                'status'  => false,
                'message' => '',
                '_time'   => (int) ((microtime(true) * 1000) - $start) .'ms',
            ]);
        }

        return $this->json_response([
            'status'  => true,
            'message' => '',
            'data'    => $result,
            '_time'   => (int) ((microtime(true) * 1000) - $start) .'ms',
        ]);
    }
}
