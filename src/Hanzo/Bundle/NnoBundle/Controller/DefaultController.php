<?php

namespace Hanzo\Bundle\NnoBundle\Controller;

use InvalidArgumentException;
use Hanzo\Core\CoreController;

class DefaultController extends CoreController
{
    /**
     * Phone number form widget
     * @return Response
     */
    public function widgetAction()
    {
        return $this->render('NnoBundle:Default:widget.html.twig');
    }


    /**
     * Number lookup
     *
     * @param  integer $number Phone number to lookup
     * @return Response
     */
    public function lookupAction($number)
    {
        try {
            $address = $this->get('nno')->findOne($number);
        } catch (InvalidArgumentException $e) {
            return $this->json_response([
                'status' => FALSE,
                'message' => $this->get('translator')->trans('create.nno.no_address_found', [], 'account'),
            ]);
        }

        if ($address) {
            return $this->json_response([
                'status' => TRUE,
                'message' => $this->get('translator')->trans('create.nno.address_found', [], 'account'),
                'data' => $address
            ]);
        }

        return $this->json_response([
            'status' => FALSE,
            'message' => $this->get('translator')->trans('create.nno.no_address_found', [], 'account'),
        ]);
    }
}
