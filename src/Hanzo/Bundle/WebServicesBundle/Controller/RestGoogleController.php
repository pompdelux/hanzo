<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;

/**
 * @see
 *  http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 *  http://speakerdeck.com/u/hhamon/p/silex-meets-soap-rest
 *
 */
class RestGoogleController extends CoreController
{
    /**
     * TODO: implement documentation in index actions.
     */
    public function indexAction() {}

    public function proxyAction($query = null, $country = null)
    {
        $request = sprintf('http://maps.google.com/maps/geo?q=%s&output=json&oe=utf8', urlencode($query . ',' . $country));
        $response = array(
            'status' => TRUE,
            'data' => json_decode(file_get_contents($request))
        );

        return $this->json_response($response);
    }
}
