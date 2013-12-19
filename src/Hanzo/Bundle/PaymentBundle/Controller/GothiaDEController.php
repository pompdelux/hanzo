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
use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApi;
use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApiCallException;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class GothiaDEController extends CoreController
{
    /**
     * blockAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        $api = $this->get('payment.gothiadeapi');

        if (!$api->isActive()) {
            return new Response( '', 200, array('Content-Type' => 'text/html'));
        }

        return $this->render('PaymentBundle:GothiaDE:block.html.twig',array());
    }

    /**
     * paymentAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
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
            ->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE))
        ;

        $additional_data = [];

        // No gothia account has been created and associated with the customer, so lets do that
        if (is_null($gothiaAccount)) {
            $additional_data[] = 'social_security_num';
        }

        if ($paytype === 'gothia_lv') {
            $additional_data[] = 'bank_account_no';
            $additional_data[] = 'bank_id';
        }

        // Build the form where the customer can enter his/hers information
        $form = $this->createFormBuilder($additional_data);

        // If the is first time user, they must enter a ssn.
        if (is_null($gothiaAccount)) {
            $form = $form->add('social_security_num', 'text', array(
                'label'              => 'social_security_num',
                'required'           => true,
                'translation_domain' => 'gothia'
            ));
        }

        if ($paytype === 'gothia_lv') {
            $form = $form->add('bank_account_no', 'text', array(
                    'label'              => 'bank_account_no',
                    'required'           => true,
                    'translation_domain' => 'gothia' ) )
                ->add('bank_id', 'text', array(
                    'label'              => 'bank_id',
                    'required'           => true,
                    'translation_domain' => 'gothia' ) );
        }
        $form = $form->getForm();

        return $this->render('PaymentBundle:GothiaDE:payment.html.twig',array(
            'page_type'       => 'gothia',
            'form'            => $form->createView(),
            'skip_my_account' => true,
        ));
    }

    /**
     * confirmAction
     * @param Request $request
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function confirmAction(Request $request)
    {
        $order              = OrdersPeer::getCurrent(true);
        $customer           = $order->getCustomers(Propel::getConnection(null, Propel::CONNECTION_WRITE));

        $api                = $this->get('payment.gothiadeapi');

        $hanzo              = Hanzo::getInstance();
        $translator         = $this->get('translator');

        $domainKey          = $hanzo->get('core.domain_key');
        $form               = $request->request->get('form');
        $SSN                = isset($form['social_security_num']) ? $form['social_security_num'] : NULL;
        // Direct Debit - Gothia_LV
        $bank_account_no    = isset($form['bank_account_no']) ? $form['bank_account_no'] : NULL;
        $bank_id            = isset($form['bank_id']) ? $form['bank_id'] : NULL;

        $timer = new Timer('gothia', true);

        if (!$customer instanceof Customers) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.checkcustomer.failed', ['%msg%' => 'no customer'], 'gothia'),
            ));
        }

        // Use form validation?
        if ($SSN) {

            switch (str_replace('Sales', '', $domainKey)) {
                case 'FI':
                    /**
                     * Finland uses social security numbers with dash DDMMYY-CCCC
                     */
                    if(!strpos($SSN, '-')){ // FI has to have dash. If it isnt there, add it. Could be made better?
                        $SSN = substr($SSN, 0, 6).'-'.substr($SSN, 6);
                    }

                    if (strlen($SSN) < 11) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
                        ));
                    }

                    if (strlen($SSN) > 11) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.to_long', array(), 'gothia')
                        ));
                    }
                    break;
                case 'NO':
                    /**
                     * Norway uses social security numbers without dash but with 5 digits DDMMYY-CCCCC
                     */
                    $SSN = strtr( $SSN, array( '-' => '', ' ' => '' ) );

                    if (strlen($SSN) < 11) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
                        ));
                    }

                    if (strlen($SSN) > 11) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.to_long', array(), 'gothia')
                        ));
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

                    $SSN = strtr( $SSN, array( '-' => '', ' ' => '' ) );

                    if (strlen($SSN) < 8) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
                        ));
                    }

                    if (strlen($SSN) > 8) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.to_long', array(), 'gothia')
                        ));
                    }
                    break;
                default:
                    /**
                     * All others uses social security number without dash DDMMYYCCCC
                     */

                    $SSN = strtr( $SSN, array( '-' => '', ' ' => '' ) );

                    //Every other domain
                    if (!is_numeric($SSN)) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.not_numeric', array(), 'gothia'),
                        ));
                    }
                    if (strlen($SSN) < 10) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
                        ));
                    }

                    if (strlen($SSN) > 10) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.ssn.to_long', array(), 'gothia')
                        ));
                    }
                    break;
            }

            $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
            if (is_null($gothiaAccount)) {
                $gothiaAccount = new GothiaAccounts();
            }

            $gothiaAccount->setDistributionBy( 'NotSet' )
                ->setDistributionType( 'NotSet' )
                ->setSocialSecurityNum( $SSN )
                ->setCustomersId( $customer->getId());
            $customer->setGothiaAccounts( $gothiaAccount );
        }
        else {

            $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
            if ($gothiaAccount instanceof GothiaAccounts) {
                $SSN = $gothiaAccount->getSocialSecurityNum();
            }
            else {
                Tools::debug('Customer has no SSN . This is weird!', __METHOD__, array());
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.placereservation.error', array(), 'gothia'),
                ));
            }
        }

        // Validate bank info when using Gothia LV payments.
        // Validation is per domain.
        if($order->getPaymentPaytype() === 'gothia_lv') {
            switch (str_replace('Sales', '', $domainKey)) {
                case 'DE':
                    /**
                     * Deutchland:
                     *   Bank account <=10
                     *   Bank id      =8
                     */

                    if ((strlen($bank_account_no) > 10 || !is_numeric($bank_account_no))) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.bank_account_no.to_long', array(), 'gothia')
                        ));
                    }
                    if ((strlen($bank_id) != 8 || !is_numeric($bank_id))) {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.bank_id.to_long', array(), 'gothia')
                        ));
                    }
                    break;
            }

            // Update the order object with LV data.
            $order->setAttribute('bank_account_no', 'payment', $bank_account_no);
            $order->setAttribute('bank_id', 'payment', $bank_id);

            $order->save();
        }


        if ( $order->getState() > Orders::STATE_PRE_PAYMENT )
        {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.order.state_pre_payment.locked', array(), 'gothia'),
            ));
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
                if ((in_array($paytype, array('gothia', 'gothiade'))) && ($order->getTotalPrice() != $oldOrder->getTotalPrice())) {
                    try {
                        $response = $api->call()->cancelReservation($customer, $oldOrder);
                    } catch (GothiaApiCallException $e) {
                        $timer->logOne('cancelReservation call failed, orderId #'.$oldOrder->getId());
                        Tools::debug('Cancel reservation failed', __METHOD__, array('Message' => $e->getMessage()));

                        return $this->json_response(array(
                            'status' => false,
                            'message' => $translator->trans('json.cancelreservation.failed', array('%msg%' => $e->getMessage()), 'gothia'),
                        ));
                    }

                    $timer->logOne('cancelReservation, orderId #'.$oldOrder->getId());

                    if ( $response->isError() ) {
                        return $this->json_response(array(
                            'status' => false,
                            'message' => $translator->trans('json.cancelreservation.error', array(), 'gothia'),
                        ));
                    }
                }
            }
        }

        try {
            $parameters = null;
            if ($order->getPaymentPaytype() === 'gothia_lv') {
                $parameters = array(
                    'bank_account_no' => $bank_account_no,
                    'bank_id' => $bank_id,
                    'payment_method' => 'DirectDebet'
                );
            }
            $response = $api->call()->checkCustomerAndPlaceReservation($customer, $order, $parameters);
            $timer->logOne('checkCustomerAndPlaceReservation orderId #'.$order->getId());
        } catch ( GothiaApiCallException $e ) {
            if (Tools::isBellcomRequest()) {
                Tools::debug('Check Customer and Place Reservation Exception', __METHOD__, array('Message' => $e->getMessage()));
            }
            $api->updateOrderFailed($request, $order);
            Tools::debug('CheckCustomerAndPlaceReservation failed', __METHOD__, array('Data' => $response));

            return $this->json_response(array(
                'status' => false,
                'message' => $translator->trans('json.placereservation.failed', array('%msg%' => $e->getMessage()), 'gothia'),
            ));
        }

        // NICETO: priority: low, refacture gothia to look more like DibsController

        try {
            $api->updateOrderSuccess($request, $order);
            $gothiaAccount->save();
            $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));

            return $this->json_response(array(
                'status' => true,
                'message' => '',
            ));
        } catch (Exception $e) {
            if (Tools::isBellcomRequest()) {
                Tools::debug('Place Reservation Exception', __METHOD__, array('Message' => $e->getMessage()));
            }
            $api->updateOrderFailed($request, $order);

            Tools::debug('Place reservation failed', __METHOD__, array('Message' => $e->getMessage()));

            return $this->json_response(array(
                'status' => false,
                'message' => $translator->trans('json.placereservation.error', array(), 'gothia'),
            ));
        }
    }

    /**
     * testAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function testAction()
    {
        $customer  = CustomersPeer::getCurrent();
        $order     = OrdersPeer::getCurrent();

        $api = $this->get('payment.gothiadeapi');
        $response = $api->call()->checkCustomer( $customer, $order );

        error_log(__LINE__.':'.__FILE__.' '.print_r($response,1)); // hf@bellcom.dk debugging

        return new Response( 'Test completed', 200, array('Content-Type' => 'text/html'));
    }

    /**
     * processAction
     *
     * @return object Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function processAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( $order->getState() < Orders::STATE_PAYMENT_OK ) {
            return $this->redirect($this->generateUrl('_checkout_failed'));
        }

        return $this->redirect($this->generateUrl('_checkout_success'));
    }
}
