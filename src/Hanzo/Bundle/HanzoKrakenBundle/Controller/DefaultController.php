<?php

namespace Hanzo\Bundle\HanzoKrakenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('HanzoKrakenBundle:Default:index.html.twig', array('name' => $name));
    }
}
