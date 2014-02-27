<?php

namespace Hanzo\Bundle\RMABundle\Controller;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Addresses;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DefaultController
 */
class DefaultController extends Controller
{

    /**
     * Action formAction.
     */
    public function formAction(Request $request, $order_id)
    {
        $order = OrdersQuery::create()
            ->joinWithOrdersLines()
            ->filterByCustomersId(CustomersPeer::getCurrent()->getId())
            ->findPk($order_id);

        if (!$order instanceof Orders || $order->getState() != Orders::STATE_SHIPPED) {
            $this->get('session')->setFlash('notice', $this->get('translator')->trans('rma.not_allowed_wrong_state', [], 'rma'));
            return $this->redirect($this->generateUrl('_account'));
        }

        $order_lines = $order->getOrdersLiness();

        $products = [];
        foreach ($order_lines as $order_line) {
            if ($order_line->getType() == 'product') {

                $product = $order_line->toArray(\BasePeer::TYPE_FIELDNAME);

                // Only generate an image if the product still exists.
                if (isset($product['products_id'])) {
                    $product['basket_image']
                        = preg_replace('/[^a-z0-9]/i', '-', $product['products_name']) .
                        '_' .
                        preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $product['products_color'])) .
                        '_overview_01.jpg';
                }
                $products['product_' . $product['id']] = $product;
            }
        }
\Hanzo\Core\Tools::log($products);
        $rma_products = json_decode($request->query->get('products'), true);
        // Time to generate some pdf's!
        if (count($rma_products)) {

            // Generate an address for the delivery address of this order. Used
            // in the rma pdf.
            $address = new Addresses();
            $address->setTitle($order->getDeliveryTitle());
            $address->setFirstName($order->getDeliveryFirstName());
            $address->setLastName($order->getDeliveryLastName());
            $address->setAddressLine1($order->getDeliveryAddressLine1());
            $address->setAddressLine2($order->getDeliveryAddressLine2());
            $address->setPostalCode($order->getDeliveryPostalCode());
            $address->setCity($order->getDeliveryCity());
            $address->setCountry($order->getDeliveryCountry());
            $address->setStateProvince($order->getDeliveryStateProvince());
            $address->setCompanyName($order->getDeliveryCompanyName());

            $address_formatter = $this->get('hanzo.address_formatter');

            $address_block = mb_convert_encoding($address_formatter->format($address), 'HTML-ENTITIES', 'UTF-8');

            // Only show the products which are choosed to RMA.
            foreach ($rma_products as &$rma_product) {
                if (isset($products['product_' . $rma_product['id']])) {
                    $rma_product['rma_description'] = mb_convert_encoding($rma_product['rma_description'], 'HTML-ENTITIES', 'UTF-8');
                    $rma_product['rma_cause'] = mb_convert_encoding($rma_product['rma_cause'], 'HTML-ENTITIES', 'UTF-8');
                    $rma_product += $products['product_' . $rma_product['id']];
                }
            }

            $html = $this->renderView(
                'RMABundle:Default:rma.html.twig', array(
                    'products'  => $rma_products,
                    'order' => $order,
                    'customer' => CustomersPeer::getCurrent(),
                    'address_block' => $address_block,
                )
            );

            // Return the generated PDF directly as a reponse.
            return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename="POMPdeLUX_RMA_' . $order_id . '.pdf"',
                )
            );
        } else {
            return $this->render(
                'RMABundle:Default:form.html.twig', array(
                    'order' => $order,
                    'order_lines' => $products,
                    'page_type' => 'rma',
                )
            );
        }

    }
}
