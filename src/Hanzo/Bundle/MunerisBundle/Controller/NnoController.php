<?php

namespace Hanzo\Bundle\MunerisBundle\Controller;

use Hanzo\Core\CoreController;
use Guzzle\Http\Exception\ClientErrorResponseException;

class NnoController extends CoreController
{
    public function lookupAction($number)
    {
        $start = (microtime(true) * 1000);
        $guzzle = $this->get('muneris.guzzle.client');

        $request = $guzzle->get('/nno/numbers/'.$number);
        $request->setHeader('Accept', 'application/json');

        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            return $this->json_response([
                'status'  => false,
                'message' => $this->get('translator')->trans('create.nno.no_address_found', [], 'account'),
                '_time'   => (int) ((microtime(true) * 1000) - $start) .'ms',
            ]);
        }

        return $this->json_response([
            'status'  => true,
            'message' => '',
            'data'    => json_decode($response->getBody()),
            '_time'   => (int) ((microtime(true) * 1000) - $start) .'ms',
        ]);
    }
}
