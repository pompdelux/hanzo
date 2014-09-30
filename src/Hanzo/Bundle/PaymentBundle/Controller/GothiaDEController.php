<?php
/**
 * @file GothiaDEController.php
 *
 * Originally copied from GothiaController.php
 *
 * This dublicate works as an individually payment type, only used in germany.
 * Complete other customerflow, therefore new controller.
 *
 */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Propel;
use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApiCallException;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

/**
 * Class GothiaDEController
 *
 * @package Hanzo\Bundle\PaymentBundle
 */
class GothiaDEController extends CoreController
{
    /**
     * blockAction
     *
     * @return Response
     */
    public function blockAction()
    {
        $api = $this->get('payment.gothiadeapi');

        if (!$api->isActive()) {
            return new Response('', 200, ['Content-Type' => 'text/html']);
        }

        return $this->render('PaymentBundle:GothiaDE:block.html.twig', []);
    }

    /**
     * paymentAction
     *
     * @return Response
     */
    public function paymentAction()
    {
        $order = OrdersPeer::getCurrent();
        // Difference between gothia and gothia_lv payments.
        $paytype = $order->getPaymentPaytype();

        if ($order->isNew()) {
            return $this->redirect($this->generateUrl('_checkout'));
        }

        // hf@bellcom.dk, 18-sep-2012: maybe a fix for orders contaning valid dibs info and then is overriden with gothia billingmethod -->>
        if ($order->getState() > Orders::STATE_PRE_PAYMENT) {
            $this->get('session')->setFlash('notice', 'order.state_pre_payment.locked');

            return $this->redirect($this->generateUrl('basket_view'));
        }
        // <<-- hf@bellcom.dk, 18-sep-2012: maybe a fix for orders contaning valid dibs info and then is overriden with gothia billingmethod
        //
        $gothiaAccount = $order
            ->getCustomers(Propel::getConnection(null, Propel::CONNECTION_WRITE))
            ->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));

        $additionalData = [];

        // No gothia account has been created and associated with the customer, so lets do that
        if (is_null($gothiaAccount)) {
            $additionalData[] = 'social_security_num';
        }

        if ($paytype === 'gothia_lv') {
            $additionalData[] = 'bank_account_no';
            $additionalData[] = 'bank_id';
        }

        // Build the form where the customer can enter his/hers information
        $form = $this->createFormBuilder($additionalData);

        // If the is first time user, they must enter a ssn.
        if (is_null($gothiaAccount)) {
            $form = $form->add('social_security_num', 'text', [
                'label'              => 'social_security_num',
                'required'           => true,
                'translation_domain' => 'gothia'
            ]);
        }

        if ($paytype === 'gothia_lv') {
            $form = $form->add('bank_account_no', 'text', [
                'label'              => 'bank_account_no',
                'required'           => true,
                'translation_domain' => 'gothia'
            ])->add('bank_id', 'text', [
                'label'              => 'bank_id',
                'required'           => true,
                'translation_domain' => 'gothia'
            ]);
        }
        $form = $form->getForm();

        return $this->render('PaymentBundle:GothiaDE:payment.html.twig', [
            'page_type'       => 'gothia',
            'form'            => $form->createView(),
            'skip_my_account' => true,
        ]);
    }

    /**
     * confirmAction
     *
     * @param Request  $request
     *
     * @return Response
     */
    public function confirmAction(Request $request)
    {
        $order              = OrdersPeer::getCurrent(true);
        $customer           = $order->getCustomers(Propel::getConnection(null, Propel::CONNECTION_WRITE));

        $api                = $this->get('payment.gothiadeapi');

        $hanzo              = Hanzo::getInstance();
        $translator         = $this->get('translator');

        $domainKey          = $hanzo->get('core.domain_key');
        $form               = $request->request->get('form');
        $SSN                = isset($form['social_security_num']) ? $form['social_security_num'] : null;
        // Direct Debit - Gothia_LV
        $bankAccountNo      = isset($form['bank_account_no']) ? $form['bank_account_no'] : null;
        $bankId             = isset($form['bank_id']) ? $form['bank_id'] : null;

        $timer = new Timer('gothia', true);

        if (!$customer instanceof Customers) {
            return $this->json_response([
                'status'  => false,
                'message' => $translator->trans('json.checkcustomer.failed', ['%msg%' => 'no customer'], 'gothia'),
            ]);
        }

        // Use form validation?
        if ($SSN) {
            switch (str_replace('Sales', '', $domainKey)) {
                case 'FI':
                    /**
                     * Finland uses social security numbers with dash DDMMYY-CCCC
                     * - FI has to have dash. If it isnt there, add it. Could be made better?
                     */
                    if (!strpos($SSN, '-')) {
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
                            'message' => $translator->trans('json.ssn.to_long', [], 'gothia')
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
                            'message' => $translator->trans('json.ssn.to_long', [], 'gothia')
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
                            'message' => $translator->trans('json.ssn.to_long', [], 'gothia')
                        ]);
                    }
                    break;
                default:
                    /**
                     * All others uses social security number without dash DDMMYYCCCC
                     */

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
                            'message' => $translator->trans('json.ssn.to_long', [], 'gothia')
                        ]);
                    }
                    break;
            }

            $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
            if (is_null($gothiaAccount)) {
                $gothiaAccount = new GothiaAccounts();
            }

            $gothiaAccount->setDistributionBy('NotSet')
                ->setDistributionType('NotSet')
                ->setSocialSecurityNum($SSN)
                ->setCustomersId($customer->getId());

            $customer->setGothiaAccounts($gothiaAccount);
        } else {

            $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
            if ($gothiaAccount instanceof GothiaAccounts) {
                $SSN = $gothiaAccount->getSocialSecurityNum();
            } else {
                Tools::debug('Customer has no SSN . This is weird!', __METHOD__, []);

                return $this->json_response([
                    'status'  => false,
                    'message' => $translator->trans('json.placereservation.error', [], 'gothia'),
                ]);
            }
        }

        // Validate bank info when using Gothia LV payments.
        // Validation is per domain.
        if ($order->getPaymentPaytype() === 'gothia_lv') {
            switch (str_replace('Sales', '', $domainKey)) {
                case 'DE':
                    /**
                     * Deutchland:
                     *   Bank account <= 34
                     *   Bank id       =  8
                     */

                    if ((strlen($bankAccountNo) > 34 || !is_numeric($bankAccountNo))) {
                        return $this->json_response([
                            'status'  => false,
                            'message' => $translator->trans('json.bank_account_no.to_long', [], 'gothia')
                        ]);
                    }
                    if ((strlen($bankId) != 8 || !is_numeric($bankId))) {
                        return $this->json_response([
                            'status'  => false,
                            'message' => $translator->trans('json.bank_id.to_long', [], 'gothia')
                        ]);
                    }
                    break;
            }

            // Update the order object with LV data.
            $order->setAttribute('bank_account_no', 'payment', $bankAccountNo);
            $order->setAttribute('bank_id', 'payment', $bankId);

            $order->save();
        }

        if ( $order->getState() > Orders::STATE_PRE_PAYMENT ) {
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
            if (!($currentVersion < 2 )) {
                $oldOrderVersion = ( $currentVersion - 1);
                $oldOrder = $order->getOrderAtVersion($oldOrderVersion);

                $paytype = strtolower($oldOrder->getBillingMethod());
                // The new order amount is different from the old order amount
                // We will remove the old reservation, and create a new one
                // but only if the old paytype was gothia
                if ((in_array($paytype, ['gothia', 'gothiade'])) && ($order->getTotalPrice() != $oldOrder->getTotalPrice())) {
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
                            'status' => false,
                            'message' => $translator->trans('json.cancelreservation.error', [], 'gothia'),
                        ]);
                    }
                }
            }
        }

        try {
            $parameters = null;
            if ($order->getPaymentPaytype() === 'gothia_lv') {
                $parameters = [
                    'bank_account_no' => $bankAccountNo,
                    'bank_id'         => $bankId,
                    'payment_method'  => 'DirectDebet'
                ];
            }
            $response = $api->call()->checkCustomerAndPlaceReservation($customer, $order, $parameters);
            $timer->logOne('checkCustomerAndPlaceReservation orderId #'.$order->getId());
        } catch ( GothiaApiCallException $e ) {

            $api->updateOrderFailed($request, $order);

            return $this->json_response([
                'status'  => false,
                'message' => $translator->trans('json.placereservation.failed', ['%msg%' => $e->getMessage()], 'gothia'),
            ]);
        }

        // NICETO: priority: low, refacture gothia to look more like DibsController

        try {
            $api->updateOrderSuccess($request, $order);
            $gothiaAccount->save();
            $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));

            return $this->json_response([
                'status'  => true,
                'message' => '',
            ]);
        } catch (Exception $e) {
            if (Tools::isBellcomRequest()) {
                Tools::debug('Place Reservation Exception', __METHOD__, ['Message' => $e->getMessage()]);
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
        $customer  = CustomersPeer::getCurrent();
        $order     = OrdersPeer::getCurrent();

        $api = $this->get('payment.gothiadeapi');
        $response = $api->call()->checkCustomer($customer, $order);

        error_log(__LINE__.':'.__FILE__.' '.print_r($response, 1));

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

        if ( $order->getState() < Orders::STATE_PAYMENT_OK ) {
            return $this->redirect($this->generateUrl('_checkout_failed'));
        }

        return $this->redirect($this->generateUrl('_checkout_success'));
    }
}
