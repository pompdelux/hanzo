<?php

namespace Hanzo\Bundle\AccountBundle\Controller;

use Hanzo\Core\CoreController,
    Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\Customers;
use Hanzo\Model\Addresses;

use Hanzo\Bundle\AccountBundle\NNO\NNO;
use Hanzo\Bundle\AccountBundle\NNO\SearchQuestion;
use Hanzo\Bundle\AccountBundle\NNO\nnoSubscriber;
use Hanzo\Bundle\AccountBundle\NNO\nnoSubscriberResult;

class NNOController extends CoreController
{
    public function widgetAction()
    {
        return $this->render('AccountBundle:NNO:widget.html.twig', array());

    }

    public function checkAction($phone)
    {
        $phone = preg_replace('/[^0-9]+/', '', $phone);

        if (substr($phone, 0, 4) == '0045') {
            $phone = substr($phone, 4);
        }
        elseif (strlen($phone) > 8 && (substr($phone, 0, 2) == '45')) {
            $phone = substr($phone, 2);
        }

        $lookup = new SearchQuestion();
        $lookup->phone = $phone;
        $lookup->username = 'delux';

        $nno = new NNO();
        $result = $nno->lookupSubscribers($lookup);

        if (($result instanceof nnoSubscriberResult) &&
            (count($result->subscribers) == 1) &&
            ($result->subscribers[0] instanceof nnoSubscriber)
        ) {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('create.nno.address_found', array(), 'account'),
                'data' => (array) $result->subscribers[0]
            ));
        }

        return $this->json_response(array(
            'status' => FALSE,
            'message' => $this->get('translator')->trans('create.nno.no_address_found', array(), 'account'),
        ));
    }
}
