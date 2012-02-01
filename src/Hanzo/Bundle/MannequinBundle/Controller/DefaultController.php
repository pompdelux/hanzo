<?php

namespace Hanzo\Bundle\MannequinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

#use Symfony\Component\EventDispatcher\EventDispatcher;
#use Hanzo\Bundle\MannequinBundle\HanzoTestEvent;

class DefaultController extends Controller
{
    public function indexAction($name = 'flaff')
    {
#        $this->get('event_dispatcher')->dispatch('xo.event', new HanzoTestEvent('ulrik was here'));
        return $this->render('HanzoMannequinBundle:Default:index.html.twig', array('name' => $name, 'page_type' => 'mannequin'));
    }
}
