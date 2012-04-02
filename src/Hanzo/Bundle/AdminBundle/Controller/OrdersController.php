<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class OrdersController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }
}
