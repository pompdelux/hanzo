<?php

namespace Hanzo\Bundle\RMABundle\Controller;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Addresses;
use Hanzo\Core\CoreController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DefaultController
 */
class DefaultController extends CoreController
{

    /**
     * Action formAction.
     */
    public function formAction(Request $request, $order_id)
    {
        $order = OrdersQuery::create()
            ->joinWithOrdersLines()
            ->useOrdersLinesQuery()
                ->leftJoinWithProducts()
            ->endUse()
            ->filterByCustomersId(CustomersPeer::getCurrent()->getId())
            ->findPk($order_id);

        if (!$order instanceof Orders || $order->getState() != Orders::STATE_SHIPPED) {
            $this->get('session')->getFlashBag()->add('notice', $this->get('translator')->trans('rma.not_allowed_wrong_state', [], 'rma'));
            return $this->redirect($this->generateUrl('_account'));
        }

        $order_lines = $order->getOrdersLiness();

        $products = [];
        foreach ($order_lines as $order_line) {
            if ($order_line->getType() == 'product') {

                $product = $order_line->getProducts();
                $product_line = $order_line->toArray(\BasePeer::TYPE_FIELDNAME);

                // Only generate an image if the product still exists.
                if ($product) {
                    $product_line['basket_image']
                        = preg_replace('/[^a-z0-9]/i', '-', $product->getMaster()) .
                        '_' .
                        preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $product_line['products_color'])) .
                        '_overview_01.jpg';
                }
                $products['product_' . $product_line['id']] = $product_line;
            }
        }

        $rma_products = json_decode($request->request->get('products'), true);
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


            $this->setCache('rma_generated_html.' . $order_id . '.' . CustomersPeer::getCurrent()->getId(), $html);

            $pdf_data = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);
            $pdf_name = 'POMPdeLUX_RMA_' . $order_id . '.pdf';

            try {
                $mail = $this->container->get('mail_manager');
                $mail->addAttachment($pdf_data, false, $pdf_name);
                $mail->setMessage('order.rma', []);
                $mail->setTo($order->getEmail(), $order->getCustomersName());

                if ($bcc = Tools::getBccEmailAddress('rma', $order)) {
                    $mail->setBcc($bcc);
                }

                $mail->send();
            } catch (\Exception $e) {}

            // Return the generated PDF directly as a response.
            return new Response(
                $pdf_data,
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename="'.$pdf_name,
                )
            );
        } else {
            $cached = $this->getCache('rma_generated_html.' . $order_id . '.' . CustomersPeer::getCurrent()->getId());
            return $this->render(
                'RMABundle:Default:form.html.twig', array(
                    'order' => $order,
                    'order_lines' => $products,
                    'page_type' => 'rma',
                    'is_cached' => $cached,
                )
            );
        }
    }

    public function getAction(Request $request, $order_id, $pdf = false) {

        if ($html = $this->getCache('rma_generated_html.' . $order_id . '.' . CustomersPeer::getCurrent()->getId())) {
            if ($pdf) {
                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    array(
                        'Content-Type'          => 'application/pdf',
                        'Content-Disposition'   => 'attachment; filename="POMPdeLUX_RMA_' . $order_id . '.pdf"',
                    )
                );
            } else {
                return $this->response($html);
            }
        }
        return $this->redirect($this->generateUrl('rma_form', array('order_id' => $order_id)));
    }

}
