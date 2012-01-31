<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\CheckoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request
    ;

use Hanzo\Core\Hanzo,
    Hanzo\Core\CoreController,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer
    ;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        return $this->render('CheckoutBundle:Default:index.html.twig',array('page_type'=>'checkout'));
    }

    /**
     * updateAction
     * @param string $block The block that has been updated
     * @param string $state State of the block
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateAction($block, $state)
    {
        $order = OrdersPeer::getCurrent();
        $orderAttributes = $order->getOrdersAttributess();

        if ( $state === 'false' )
        {
            foreach ( $orderAttributes as $att ) 
            {
                switch ($att->getNs()) 
                {
                    case 'shipping':
                        if ( $att->getCKey() === 'method' )
                        {
                            $att->delete();
                        }
                        break;

                    case 'payment':
                        if ( $att->getCKey() === 'method' || $att->getCKey() === 'paytype' )
                        {
                            $att->delete();
                        }
                        break;
                }
            }
        }
        else
        {
            $data = $this->get('request')->get('data');

            switch ($block) 
            {
                case 'shipping':
                    $order->setAttribute( 'method', $block, $data['selected_method'] );
                    break;

                case 'payment':
                    $order->setAttribute( 'method', $block, $data['selected_method'] );
                    $order->setAttribute( 'paytype', $block, $data['selected_paytype'] );
                    break;
            }

            $order->save();
        }


        return $this->json_response(array( 
            'status' => true,
            'message' => '',
        ));
    }

    /**
     * Validate
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function validateAction()
    {
        // FIXME:
        return $this->json_response(array( 
            'status' => true,
            'message' => 'Ok',
        ));

        return $this->json_response(array( 
            'status' => false,
            'message' => 'Dette er en test fejl besked',
            'data' => array(
                'name' => 'shipping'
            ),
        ));
    }

    /**
     * summeryAction
     * @params Request
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function summeryAction(Request $request)
    {
        $order = OrdersPeer::getCurrent();
        $orderAttributes = $order->getOrdersAttributess();

        $attributes = array();

        foreach ($orderAttributes as $att) 
        {
          $attributes[$att->getNs()][$att->getCKey] = $att->value();
        }

        if ( $request->isXmlHttpRequest() )
        {
          return json_encode('hest');
        }

        return $this->render('CheckoutBundle:Default:summery.html.twig',array('order'=>$order, 'attributes' => $attributes));
    }
}
