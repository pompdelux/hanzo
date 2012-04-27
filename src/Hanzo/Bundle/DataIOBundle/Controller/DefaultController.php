<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\EventDispatcher\Event,
    Symfony\Component\EventDispatcher\EventDispatcher
    ;  

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController,
    Hanzo\Bundle\DataIOBundle\Events,
    Hanzo\Bundle\DataIOBundle\FilterUpdateEvent
    ;

class DefaultController extends CoreController
{
    public function indexAction($name)
    {
        return $this->render('DataIOBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * updateSystemAction
     *
     * Recives webhook call from github
     *
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateSystemAction()
    {
        // TODO: Verify input
        $event = new FilterUpdateEvent( 'translations' );
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(Events::updateTranslations, $event);
        return new Response();
    }
}
