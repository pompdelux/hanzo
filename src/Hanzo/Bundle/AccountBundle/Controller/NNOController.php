<?php

namespace Hanzo\Bundle\AccountBundle\Controller;

use Hanzo\Core\CoreController,
    Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\Customers;
use Hanzo\Model\Addresses;

class NNOController extends CoreController
{
    public function widgetAction()
    {
        return $this->render('AccountBundle:NNO:widget.html.twig', array());

    }
}
