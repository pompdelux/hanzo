<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class AdvisorController extends CoreController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function openHouseAction(Request $request)
    {
        return $this->render('EventsBundle:Advisor:openHouse.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function mapAction(Request $request)
    {
        return $this->render('EventsBundle:Advisor:map.html.twig');
    }
}
