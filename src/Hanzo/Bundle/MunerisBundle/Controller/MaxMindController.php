<?php

namespace Hanzo\Bundle\MunerisBundle\Controller;

use Hanzo\Core\CoreController;

class MaxMindController extends CoreController
{
    public function lookupAction($ip = '')
    {
        $start = (microtime(true) * 1000);
        $service = $this->get('muneris.maxmind');

        return $this->json_response([
            'status'  => true,
            'message' => '',
            'data'    => $service->lookup($ip),
            '_time'   => (int) ((microtime(true) * 1000) - $start) .'ms',
        ]);
    }
}
