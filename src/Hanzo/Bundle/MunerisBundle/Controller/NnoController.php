<?php

namespace Hanzo\Bundle\MunerisBundle\Controller;

use Hanzo\Core\CoreController;

class NnoController extends CoreController
{
    public function lookupAction($number)
    {
        $start = (microtime(true) * 1000);
        $guzzle = $this->get('muneris.guzzle.client');

        $request = $guzzle->get('/nno/numbers/'.$number);
        $request->setHeader('Accept', 'application/json');

        $response = $request->send();
        $body     = $response->getBody();

        return $this->json_response([
            'status'  => true,
            'message' => '',
            'data'    => json_decode($body),
            '_time'   => (int) ((microtime(true) * 1000) - $start) .'ms',
        ]);
    }
}
