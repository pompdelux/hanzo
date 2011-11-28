<?php

namespace Hanzo\Bundle\MannequinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('MannequinBundle:Default:index.html.twig', array('name' => $name));
    }
}
