<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\CheckoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\CoreController
    ;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        return $this->render('CheckoutBundle:Default:index.html.twig',array('page_type'=>'checkout'));
    }

    /**
     * stateAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function stateAction()
    {
        // FIXME:
        /*return $this->json_response(array( 
            'status' => true,
            'message' => 'Ok',
        ));*/

        return $this->json_response(array( 
            'status' => false,
            'message' => 'Dette er en test fejl besked',
            'data' => array(
                'name' => 'shipping'
                ),
        ));
    }
}
