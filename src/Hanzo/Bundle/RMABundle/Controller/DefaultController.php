<?php

namespace Hanzo\Bundle\RMABundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Addresses;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\RMABundle
 */
class DefaultController extends CoreController
{

    /**
     * Action formAction.
     *
     * @param Request $request
     * @param int     $order_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
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

        $orderLines = $order->getOrdersLiness();

        $products = [];
        foreach ($orderLines as $orderLine) {
            if ($orderLine->getType() == 'product') {

                $product = $orderLine->getProducts();
                $productLine = $orderLine->toArray(\BasePeer::TYPE_FIELDNAME);

                // Only generate an image if the product still exists.
                if ($product) {
                    $productLine['basket_image']
                        = preg_replace('/[^a-z0-9]/i', '-', $product->getMaster()) .
                        '_' .
                        preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $productLine['products_color'])) .
                        '_overview_01.jpg';
                }
                $products['product_' . $productLine['id']] = $productLine;
            }
        }

        $rmaProducts = json_decode($request->request->get('products'), true);

        // Time to generate some pdf's!
        if (count($rmaProducts)) {

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

            $addressFormatter = $this->get('hanzo.address_formatter');
            $addressBlock     = mb_convert_encoding($addressFormatter->format($address), 'HTML-ENTITIES', 'UTF-8');

            // Only show the products which are chosed to RMA.
            foreach ($rmaProducts as &$rmaProduct) {
                if (isset($products['product_' . $rmaProduct['id']])) {
                    $rmaProduct['rma_description'] = mb_convert_encoding($rmaProduct['rma_description'], 'HTML-ENTITIES', 'UTF-8');
                    $rmaProduct['rma_cause'] = mb_convert_encoding($rmaProduct['rma_cause'], 'HTML-ENTITIES', 'UTF-8');
                    $rmaProduct += $products['product_' . $rmaProduct['id']];
                }
            }

            $html = $this->renderView('RMABundle:Default:rma.html.twig', [
                'products'      => $rmaProducts,
                'order'         => $order,
                'customer'      => CustomersPeer::getCurrent(),
                'address_block' => $addressBlock,
            ]);

            $this->setCache('rma_generated_html.' . $order_id . '.' . CustomersPeer::getCurrent()->getId(), $html);

            $pdfData = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);
            $pdfName = 'POMPdeLUX_RMA_' . $order_id . '.pdf';

            try {
                $mail = $this->container->get('mail_manager');
                $mail->setTo($order->getEmail(), $order->getCustomersName());
                $mail->addAttachment($pdfData, false, $pdfName);
                $mail->setMessage('order.rma', [
                    'order_id'      => $order_id,
                    'customer_name' => $order->getCustomersName(),
                ]);

                if ($bcc = Tools::getBccEmailAddress('rma', $order)) {
                    $mail->setBcc($bcc);
                }

                $mail->send();
            } catch (\Exception $e) {
            }

            // Return the generated PDF directly as a response.
            return new Response($pdfData, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $pdfName,
            ]);
        } else {
            $cached = $this->getCache('rma_generated_html.' . $order_id . '.' . CustomersPeer::getCurrent()->getId());

            return $this->render('RMABundle:Default:form.html.twig', [
                'order' => $order,
                'order_lines' => $products,
                'page_type' => 'rma',
                'is_cached' => $cached,
            ]);
        }
    }

    /**
     * @param int  $order_id
     * @param bool $pdf
     *
     * @return Response
     */
    public function getAction($order_id, $pdf = false)
    {
        $html = $this->getCache('rma_generated_html.' . $order_id . '.' . CustomersPeer::getCurrent()->getId());

        if ($html) {
            if ($pdf) {
                return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, [
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename="POMPdeLUX_RMA_' . $order_id . '.pdf"',
                ]);
            } else {
                return $this->response($html);
            }
        }

        return $this->redirect($this->generateUrl('rma_form', ['order_id' => $order_id]));
    }

    /**
     * @param Request $request
     */
    public function uploadCompleteAction(Request $request)
    {
        $json       = json_decode($request->getContent());
        $response   = ['error' => false, 'error_msg' => '', 'msg' => ''];
        $translator = $this->get('translator');

        // rma_upload.php checks the fields and reports any errors
        // If we at some point want to expose the detailed upload error, change error type upload in rma_upload.php to upload_XXX
        if (isset($json->errors) && !empty($json->errors))
        {
            $response['error'] = true;
            foreach ($json->errors as $error)
            {
                $response['error_msg'][] = $translator->trans('rma.claims.error.'.$error->type, [], 'rma');
            }
        }

        // Checks if rma_upload.php has returned the correct data
        $fields = ['files', 'data'];
        foreach ($fields as $field)
        {
            if (!isset($json->{$field}))
            {
                $response['error'] = true;
                $response['error_msg'][] = $translator->trans('rma.claims.error.missing_data', [], 'rma');
            }
        }

        if ( $response['error'] !== true )
        {
            $hanzo     = Hanzo::getInstance();
            $domainKey = $hanzo->get('core.domain_key');

            switch ($domainKey)
            {
                case 'AT':
                    $reciever = 'claimat@pompdelux.com';
                    break;
                case 'CH':
                    $reciever = 'claimch@pompdelux.com';
                    break;
                case 'COM':
                    $reciever = 'claimcom@pompdelux.com';
                    break;
                case 'DE':
                    $reciever = 'claimde@pompdelux.com';
                    break;
                case 'DK':
                    $reciever = 'claimdk@pompdelux.com';
                    break;
                case 'FI':
                    $reciever = 'claimfi@pompdelux.com';
                    break;
                case 'NL':
                    $reciever = 'claimnl@pompdelux.com';
                    break;
                case 'NO':
                    $reciever = 'claimno@pompdelux.com';
                    break;
                case 'SE':
                    $reciever = 'claimse@pompdelux.com';
                    break;
            }

            try {
                $mail = $this->container->get('mail_manager');
                $mail->setTo($reciever, 'Claims');

                /*
                 * Does not work with amazon
                 * $sender       = $json->data->email;
                 * $name         = $json->data->name;
                 * $mail->setReplyTo($sender, $name)
                 * ->setSender($sender, $name);
                 */

                $mail->setMessage('rma.claims', [
                    'data' => $json->data,
                    'files' => $json->files,
                ]);

                $mail->send();

                $response['msg'] = $translator->trans('rma.claims.success', [], 'rma');
            } catch (\Exception $e) {
                $response['error'] = true;
                $response['error_msg'][] = 'Failed sending claim';
            }
        }

        return $this->json_response( $response );
    }
}
