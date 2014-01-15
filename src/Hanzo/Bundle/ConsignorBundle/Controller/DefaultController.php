<?php

namespace Hanzo\Bundle\ConsignorBundle\Controller;

use Hanzo\Bundle\ConsignorBundle\Services\ShipmentServer\ConsignorAddress;
use Hanzo\Model\Orders;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DefaultController extends Controller
{
    /**
     * @Route("/account/consignor/return-label/{id}", name="consignor_return_label")
     * @ParamConverter("post", class="Hanzo\Model\Orders")
     */
    public function indexAction(Orders $order)
    {
        $to = new ConsignorAddress(1, 'ulrik nielsen', 'thurasvej 12', '', 6000, 'kolding', 'DK');

        $shipment = $this->container->get('consignor.service.submit_shipment');
        $shipment->setOrderId($order->getId());
        $shipment->setToAddress(new ConsignorAddress(
            1,
            'POMPdeLUX A/S',
            'Møglhøj vej 1',
            '',
            1234,
            'Århus',
            'DK',
            'RETUR'
        ));
        // from address should always be the billing address of the order.
        $shipment->setFromAddress(new ConsignorAddress(
            2,
            $order->getBillingFirstName().' '.$order->getBillingLastName(),
            $order->getBillingAddressLine1(),
            $order->getBillingAddressLine2(),
            $order->getBillingPostalCode(),
            $order->getBillingCity(),
            $order->getBillingCountry() // needs to be converted to ISO-2
        ));

        try {
            $label = $shipment->fetchReturnLabel();
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Could not generate return label for order id: '.$order->getId());

            return $this->redirect($this->generateUrl('_account'));
        }

        // we got the label from consignor, let's send it to the user.
        $response = new Response($label, 200, [
            'Content-Description'       => 'File Transfer',
            'Content-type'              => 'application/octet-stream',
            'Content-Disposition'       => 'attachment; filename=lable.pdf',
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Length'            => strlen($label)
        ]);
        $response->setContent($label);

        return $response;
    }
}
