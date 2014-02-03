<?php

namespace Hanzo\Bundle\ConsignorBundle\Controller;

use Hanzo\Bundle\ConsignorBundle\Services\ShipmentServer\ConsignorAddress;
use Hanzo\Model\Orders;
use Hanzo\Model\CountriesQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DefaultController extends Controller
{
    /**
     * Triggers call to edi-soft consignor to get return label as pdf.
     *
     * Note: Do not change the name in the Route definition without changing it in the service as well!
     *
     * @Route("/account/consignor/return-label/{id}", name="consignor_return_label")
     * @ParamConverter("post", class="Hanzo\Model\Orders")
     */
    public function consignorReturnLabelAction(Orders $order)
    {
        $shipment = $this->container->get('consignor.service.submit_shipment');

        if (false ===$shipment->isEnabled()) {
            return $this->redirect($this->generateUrl('_account'));
        }

        $shipment->setOrderId($order->getId());

        // from address should always be the billing address of the order.
        $shipment->setFromAddress(new ConsignorAddress(
            2,
            $order->getBillingFirstName().' '.$order->getBillingLastName(),
            $order->getBillingAddressLine1(),
            $order->getBillingAddressLine2(),
            $order->getBillingPostalCode(),
            $order->getBillingCity(),
            self::countryIdToIso2($order->getBillingCountriesId()),
            $order->getEmail(),
            $order->getPhone()
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
            'Content-type'              => 'application/pdf',
            'Content-Disposition'       => 'attachment; filename=label-'.$order->getId().'-'.date('YmdHi').'.pdf',
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Length'            => strlen($label)
        ]);
        $response->setContent($label);

        return $response;
    }


    /**
     * @param $id
     * @return mixed
     */
    protected static function countryIdToIso2($id)
    {
        return CountriesQuery::create()
            ->select('Iso2')
            ->findOneById($id)
        ;
    }
}
