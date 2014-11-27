<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Propel;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Timer;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\GothiaAccounts;
use Hanzo\Core\CoreController;
use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApi;
use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApiCallException;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

/**
 * Class GothiaController
 *
 * @package Hanzo\Bundle\PaymentBundle
 */
class GothiaController extends CoreController
{
    /**
     * blockAction
     *
     * @return Response
     */
    public function blockAction()
    {
        $api = $this->get('payment.gothiaapi');

        if (!$api->isActive()) {
            return new Response('', 200, ['Content-Type' => 'text/html']);
        }

        return $this->render('PaymentBundle:Gothia:block.html.twig', []);
    }

    /**
     * paymentAction
     *
     * @return Response
     */
    public function paymentAction()
    {
        $order = OrdersPeer::getCurrent();

        if ($order->isNew()) {
            return $this->redirect($this->generateUrl('_checkout'));
        }

        // maybe a fix for orders contaning valid dibs info and then is overriden with gothia billingmethod
        if ($order->getState() > Orders::STATE_PRE_PAYMENT) {
            $this->get('session')->getFlashBag()->add('notice', 'order.state_pre_payment.locked');

            return $this->redirect($this->generateUrl('basket_view'));
        }

        $gothiaAccount = $order
            ->getCustomers(Propel::getConnection(null, Propel::CONNECTION_WRITE))
            ->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));

        // No gothia account has been created and associated with the customer, so lets do that
        if (is_null($gothiaAccount)) {
            $gothiaAccount = new GothiaAccounts();
        }

        // Build the form where the customer can enter his/hers information
        $form = $this->createFormBuilder($gothiaAccount)
            ->add('social_security_num', 'text', [
                'label'              => 'social_security_num',
                'required'           => true,
                'translation_domain' => 'gothia'
            ])->getForm();

        return $this->render('PaymentBundle:Gothia:payment.html.twig', [
            'page_type'       => 'gothia',
            'form'            => $form->createView(),
            'skip_my_account' => true,
        ]);
    }

    /**
     * checkCustomerAction
     * The form has been submitted via ajax -> process it
     *
     * @param Request  $request
     *
     * @return Response
     */
    public function checkCustomerAction(Request $request)
    {
        $form       = $request->request->get('form');
        $SSN        = $form['social_security_num'];
        $translator = $this->get('translator');
        $hanzo      = Hanzo::getInstance();
        $domainKey  = $hanzo->get('core.domain_key');

        // Use form validation ??
        switch (str_replace('Sales', '', $domainKey)) {
            case 'FI':
                // Finland uses social security numbers with dash DDMMYY-CCCC
                if (!strpos($SSN, '-')) {
                    // FI has to have dash. If it isnt there, add it. Could be made better?
                    $SSN = substr($SSN, 0, 6).'-'.substr($SSN, 6);
                }

                if (strlen($SSN) < 11) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.to_short', [], 'gothia'),
                    ]);
                }

                if (strlen($SSN) > 11) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.to_long', [], 'gothia'),
                    ]);
                }
                break;

            case 'NO':
                /**
                 * Norway uses social security numbers without dash but with 5 digits DDMMYY-CCCCC
                 */
                $SSN = strtr($SSN, ['-' => '', ' ' => '']);
                if (strlen($SSN) < 11) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.to_short', [], 'gothia'),
                    ]);
                }

                if (strlen($SSN) > 11) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.to_long', [], 'gothia'),
                    ]);
                }
                break;

            case 'DK':
            case 'NL':
            case 'DE':
            case 'COM':
                /**
                 * Denmark uses birthdate DDMMYYYY
                 * Netherland uses birthdate DDMMYYYY
                 * Germany uses birthdate DDMMYYYY
                 */

                $SSN = strtr($SSN, ['-' => '', ' ' => '']);

                if (strlen($SSN) < 8) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.to_short', [], 'gothia'),
                    ]);
                }

                if (strlen($SSN) > 8) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.to_long', [], 'gothia'),
                    ]);
                }
                break;

            default:
                // All others uses social security number without dash DDMMYYCCCC
                $SSN = strtr($SSN, ['-' => '', ' ' => '']);

                //Every other domain
                if (!is_numeric($SSN)) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.not_numeric', [], 'gothia'),
                    ]);
                }
                if (strlen($SSN) < 10) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.to_short', [], 'gothia'),
                    ]);
                }

                if (strlen($SSN) > 10) {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $translator->trans('json.ssn.to_long', [], 'gothia'),
                    ]);
                }
                break;
        }

        $order    = OrdersPeer::getCurrent();
        $customer = $order->getCustomers(Propel::getConnection(null, Propel::CONNECTION_WRITE));

        if (!$customer instanceof Customers) {
            return $this->json_response([
                'status'  => false,
                'message' => $translator->trans('json.checkcustomer.failed', ['%msg%' => 'no customer'], 'gothia'),
            ]);
        }

        $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
        if (is_null($gothiaAccount)) {
            $gothiaAccount = new GothiaAccounts();
        }

        $gothiaAccount
            ->setDistributionBy('NotSet')
            ->setDistributionType('NotSet')
            ->setSocialSecurityNum($SSN);

        $customer->setGothiaAccounts($gothiaAccount);

        $timer = new Timer('gothia', true);

        try {
            // Validate information @ gothia
            $response = $this
                ->get('payment.gothiaapi')
                ->call()
                ->checkCustomer($customer, $order);
        } catch (GothiaApiCallException $e) {
            // All errors are thrown in an exception. No Exception, reservation
            // is approved.

            if (Tools::isBellcomRequest()) {
                Tools::debug('Check Customer Failed', __METHOD__, ['Message' => $e->getMessage()]);
            }
            $timer->logOne('checkCustomer call failed orderId #'.$order->getId());

            return $this->json_response([
                'status' => false,
                'message' => $translator->trans('json.checkcustomer.failed', ['%msg%' => $e->getMessage()], 'gothia'),
            ]);
        }

        $timer->logOne('checkCustomer call, orderId #'.$order->getId());

        $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
        $gothiaAccount
            ->setDistributionBy($response->data['DistributionBy'])
            ->setDistributionType($response->data['DistributionType']);

        $customer->setGothiaAccounts($gothiaAccount);
        $customer->save();

        return $this->json_response([
            'status'  => true,
            'message' => '',
        ]);
    }

    /**
     * confirmAction
     * @param Request $request
     *
     * @return Response
     */
    public function confirmAction(Request $request)
    {
        $order      = OrdersPeer::getCurrent(true);
        $customer   = $order->getCustomers(Propel::getConnection(null, Propel::CONNECTION_WRITE));
        $api        = $this->get('payment.gothiaapi');
        $translator = $this->get('translator');

        if ($order->getState() > Orders::STATE_PRE_PAYMENT) {
            return $this->json_response([
                'status'  => false,
                'message' => $translator->trans('json.order.state_pre_payment.locked', [], 'gothia'),
            ]);
        }

        // Handle reservations in Gothia when editing the order
        // A customer can max reserve 7.000 SEK currently, so if they edit an order to 3.500+ SEK
        // it will fail because we have not removed the old reservation first, this should fix it

        if ($order->getInEdit()) {
            $currentVersion = $order->getVersionId();

            // If the version number is less than 2 there is no previous version
            if (!($currentVersion < 2)) {
                $oldOrderVersion = ($currentVersion - 1);
                $oldOrder        = $order->getOrderAtVersion($oldOrderVersion);
                $paytype         = strtolower($oldOrder->getBillingMethod());

                // The new order amount is different from the old order amount
                // We will remove the old reservation, and create a new one
                // but only if the old paytype was gothia
                if (($paytype == 'gothia') && ($order->getTotalPrice() != $oldOrder->getTotalPrice())) {
                    $timer = new Timer('gothia', true);
                    try {
                        $response = $api->call()->cancelReservation($customer, $oldOrder);
                    } catch (GothiaApiCallException $e) {
                        $timer->logOne('cancelReservation call failed, orderId #'.$oldOrder->getId());
                        Tools::debug('Cancel reservation failed', __METHOD__, ['Message' => $e->getMessage()]);

                        return $this->json_response([
                            'status'  => false,
                            'message' => $translator->trans('json.cancelreservation.failed', ['%msg%' => $e->getMessage()], 'gothia'),
                        ]);
                    }

                    $timer->logOne('cancelReservation, orderId #'.$oldOrder->getId());

                    if ($response->isError()) {
                        return $this->json_response([
                            'status'  => false,
                            'message' => $translator->trans('json.cancelreservation.error', [], 'gothia'),
                        ]);
                    }
                }
            }
        }

        try {
            $timer    = new Timer('gothia', true);
            $response = $api->call()->placeReservation($customer, $order);
            $timer->logOne('placeReservation orderId #'.$order->getId());
        } catch (GothiaApiCallException $e) {
            $api->updateOrderFailed($request, $order);

            return $this->json_response([
                'status'  => false,
                'message' => $translator->trans('json.placereservation.failed', ['%msg%' => $e->getMessage()], 'gothia'),
            ]);
        }

        try {
            $api->updateOrderSuccess($request, $order);
            $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));

            return $this->json_response([
                'status'  => true,
                'message' => '',
            ]);
        } catch (Exception $e) {
            if (Tools::isBellcomRequest()) {
                Tools::debug('Place Reservation Exception', __METHOD__.':'.__LINE__, ['Message' => $e->getMessage()]);
            }

            $api->updateOrderFailed($request, $order);

            Tools::debug('Place reservation failed', __METHOD__, ['Message' => $e->getMessage()]);

            return $this->json_response([
                'status'  => false,
                'message' => $translator->trans('json.placereservation.error', [], 'gothia'),
            ]);
        }
    }

    /**
     * testAction
     *
     * @return Response
     */
    public function testAction()
    {
        $customer = CustomersPeer::getCurrent();
        $order    = OrdersPeer::getCurrent();

        $api = $this->get('payment.gothiaapi');
        $response = $api->call()->checkCustomer($customer, $order);

        error_log(__LINE__.':'.__FILE__.' '.print_r($response, 1)); // hf@bellcom.dk debugging

        return new Response('Test completed', 200, ['Content-Type' => 'text/html']);
    }

    /**
     * processAction
     *
     * @return object Response
     */
    public function processAction()
    {
        $order = OrdersPeer::getCurrent();

        if ($order->getState() < Orders::STATE_PAYMENT_OK) {
            return $this->redirect($this->generateUrl('_checkout_failed'));
        }

        return $this->redirect($this->generateUrl('_checkout_success'));
    }
}
