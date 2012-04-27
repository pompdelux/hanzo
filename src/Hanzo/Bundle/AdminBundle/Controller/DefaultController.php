<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\CoreController;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }
}
