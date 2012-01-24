<?php

namespace Hanzo\Bundle\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('ServiceBundle:Default:index.html.twig', array('name' => $name));
    }
}
