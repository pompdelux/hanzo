<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Gothia;

use Exception;
use Propel;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\Timer;
use Hanzo\Model\Orders;
use Hanzo\Model\Customers;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiCallInterface;

// Great... fucking oldschool crap code:
require 'AFWS.php';

/**
 * Class GothiaApiCall
 *
 * @package Hanzo\Bundle\PaymentBundle\Methods\Gothia
 */
class GothiaApiCall implements PaymentMethodApiCallInterface
{
    /**
     * undocumented class variable
     *
     * @var GothiaApiCall instance
     */
    private static $instance = null;

    /**
     * undocumented class variable
     *
     * @var GothiaApi
     */
    protected $api = null;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array     $settings
     * @param GothiaApi $api
     *
     * @return GothiaApiCall
     */
    public static function getInstance(array $settings, GothiaApi $api)
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        self::$instance->settings = $settings;
        self::$instance->api      = $api;

        return self::$instance;
    }

    /**
     * callAcquirersStatus
     *
     * @param Customers $customer
     * @param Orders    $order
     *
     * @return GothiaApiCallResponse
     */
    public function checkCustomer(Customers $customer, Orders $order)
    {
        $hanzo     = Hanzo::getInstance();
        $domainKey = str_replace('Sales', '', $hanzo->get('core.domain_key'));

        $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
        $customerId    = $customer->getId();

        if ($this->api->getTest()) {
            $customerId = $this->getTestCustomerId($gothiaAccount->getSocialSecurityNum());
        }

        if (empty($customerId)) {
            Tools::debug('Missing customer id', __METHOD__);
            $customerId = $customer->getId();
        }

        $street = trim($order->getBillingAddressLine1() . ' ' . $order->getBillingAddressLine2());

        // nl hacks
        if ('NL' === $domainKey) {
            $pcs = preg_split('/ ([0-9]+)/', $street, 2, PREG_SPLIT_DELIM_CAPTURE);

            if (count($pcs) == 3) {
                $street = $pcs[0] . ' ' . $pcs[1] . str_replace(' ', '', $pcs[2]);
            } else {
                $street = implode('', $pcs);
            }
        }

        $callString = AFSWS_CheckCustomer(
            $this->userString(),
            AFSWS_Customer(
                $street,
                $domainKey,
                $hanzo->get('core.currency'),
                $customerId,
                'Person',
                null,
                $gothiaAccount->getDistributionBy(),
                $gothiaAccount->getDistributionType(),
                $customer->getEmail(),
                null,
                $order->getBillingFirstName(),
                $order->getBillingLastName(),
                null,
                $gothiaAccount->getSocialSecurityNum(),
                $customer->getPhone(),
                str_replace(' ', '', $order->getBillingPostalCode()),
                $order->getBillingCity(),
                null
            )
        );

        $response = $this->call('CheckCustomer', $callString);

        return $response;
    }

    /**
     * @param mixed $ssn
     *
     * @return bool|int|string
     */
    public function getTestCustomerId($ssn)
    {
        $customerId = false;

        switch ($ssn) {
            case 4409291111:
                $customerId = 100010;
                break;
            case 4402181111:
                $customerId = '00100000';
                break;
            case 12053400068:
                $customerId = 100001; // .no test
                break;
            case 18106500076:
                $customerId = 100002; // .no test
                break;
            case 18106500157:
                $customerId = 100003; // .no test
                break;
            case 18126500137:
                $customerId = 100004; // .no test bad rating
                break;
            case "090260-052K": // .FI test users
                $customerId = 100106;
                break;
            case "090648-458T":
                $customerId = 100107;
                break;
            case "020185-3134":
                $customerId = 100108;
                break;
            case "010771-255U":
                $customerId = 100109;
                break;
            case "300976-787L": // Payment defaults
                $customerId = 100110;
                break;
            case "301076-0676": // -||-
                $customerId = 100111;
                break;
            case "26101945": // Danish cases
                $customerId = 100003;
                break;
            case "21111925": // Danish cases
                $customerId = 100004;
                break;
            case "21121974": // Danish cases
                $customerId = 100005;
                break;
            case "12071992": // Danish cases
                $customerId = 100006;
                break;
            case "30031997": // Danish cases
                $customerId = 100007;
                break;
            case "25031969": // Danish cases
                $customerId = 100002;
                break;
            case "01051982": // Netherland cases
                $customerId = 'T1234';
                break;

        }

        return $customerId;
    }

    /**
     * userString
     *
     * @return string
     */
    private function userString()
    {
        return AFSWS_User($this->settings['username'], $this->settings['password'], $this->settings['clientId']);
    }

    /**
     * call
     *
     * @param string $function
     * @param string $request
     *
     * @return GothiaApiCallResponse
     * @throws GothiaApiCallException
     */
    protected function call($function, $request)
    {
        $hanzo          = Hanzo::getInstance();
        $errorReporting = error_reporting(0);

        if ($this->api->getTest()) {
            $client = AFSWS_Init('test');
        } else {
            $client = AFSWS_Init('live');
        }

        $this->api->service_logger->plog($request, ['outgoing', 'payment', 'gothia', $function]);

        try {
            $response = $client->call($function, $request);
        } catch (Exception $e) {
            Tools::debug($e->getMessage(), __METHOD__, ['Function' => $function, 'Callstring' => $request]);
            throw new GothiaApiCallException($e->getMessage());
        }

        error_reporting($errorReporting);

        if ($this->api->getTest() || Tools::isBellcomRequest()) {
            Tools::debug('Gothia debug call', __METHOD__, ['Function' => $function, 'Callstring' => $request]);
            Tools::debug('Gothia debug response', __METHOD__, ['Response' => $response]);
        }

        // Examine and parse the respone recieved and determine if its an error.
        // All errors from Gothia are handled here, and returned as an GothiaApiCallException.
        $gothiaApiCallResponse = new GothiaApiCallResponse($response, $function);

        if (($response === false) || ($client->fault) || $gothiaApiCallResponse->isError()) {
            $t = $hanzo->container->get('translator');

            // Collect all errors to add into the Exception.
            $errorMessages = [
                $t->trans('We were unable to approve your payment with Gothia Invoice service.', [], 'gothia')
            ];

            $clientError = $client->getError();

            if (!empty($clientError)) {
                array_push($errorMessages, $t->trans($clientError, [], 'gothia'));
            }

            if (is_array($gothiaApiCallResponse->errors)) {
                foreach ($gothiaApiCallResponse->errors as $error) {
                    if (!empty($error) && !in_array($t->trans($error, [], 'gothia'), $errorMessages)) {
                        array_push($errorMessages, $t->trans($error, [], 'gothia'));
                    }
                }
            }

            array_push($errorMessages, $t->trans('Please contact POMPdeLUX customer service if you keep receiving this error.', [], 'gothia'));

            Tools::debug('Gothia Response Error', __METHOD__, [
                'Transaction id' => $gothiaApiCallResponse->transactionId,
                'Data'           => $gothiaApiCallResponse->data,
                'Errors'         => $gothiaApiCallResponse->errors
            ]);

            throw new GothiaApiCallException(implode('<br><br>', $errorMessages));
        }

        return $gothiaApiCallResponse;
    }

    /**
     * placeReservation
     *
     * @param Customers $customer
     * @param Orders    $order
     *
     * @return GothiaApiCallResponse
     */
    public function placeReservation(Customers $customer, Orders $order)
    {
        $amount       = number_format($order->getTotalPrice(), 2, '.', '');
        $customerId   = $customer->getId();
        $currencyCode = $order->getCurrencyCode();

        if ($this->api->getTest()) {
            $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
            $customerId    = $this->getTestCustomerId($gothiaAccount->getSocialSecurityNum());
        }

        if (empty($customerId)) {
            Tools::debug('Missing customer id', __METHOD__);
            $customerId = $customer->getId();
        }

        // hf@bellcom.dk, 29-aug-2011: remove last param to Reservation, @see comment in cancelReservation function -->>
        $callString = AFSWS_PlaceReservation(
            $this->userString(),
            AFSWS_Reservation('NoAccountOffer', $amount, $currencyCode, $customerId, null)
        );
        // <<-- hf@bellcom.dk, 29-aug-2011: remove last param to Reservation, @see comment in cancelReservation function

        $response = $this->call('PlaceReservation', $callString);

        return $response;
    }

    /**
     * @param Customers $customer
     * @param Orders    $order
     *
     * @return GothiaApiCallResponse
     */
    public function cancel(Customers $customer, Orders $order)
    {
        return $this->cancelReservation($customer, $order);
    }

    /**
     * @param Customers $customer
     * @param Orders    $order
     *
     * @return GothiaApiCallResponse
     * @throws GothiaApiCallException
     */
    public function cancelReservation(Customers $customer, Orders $order)
    {
        $timer = new Timer('gothia', true);
        $total = $order->getTotalPrice();

        if (empty($total)) {
            Tools::debug('Empty total', __METHOD__);

            throw new GothiaApiCallException('Empty total');
        }

        $amount     = number_format($total, 2, '.', '');
        $customerId = $customer->getId();

        if ($this->api->getTest()) {
            $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
            $customerId    = $this->getTestCustomerId($gothiaAccount->getSocialSecurityNum());
        }

        if (empty($customerId)) {
            Tools::debug('Missing customer id', __METHOD__);
            $customerId = $customer->getId();
        }

        if (empty($amount)) {
            Tools::debug('Empty amount', __METHOD__);

            throw new GothiaApiCallException('Empty amount');
        }

        // Gothia uses tns:CancelReservation which contains a tns:cancelReservation, therefore the 2 functions with almost the same name
        // hf@bellcom.dk, 29-aug-2011: remove 2.nd param to CancelReservationObj, pr request of Gothia... don't know why, don't care why :) -->>
        // hf@bellcom.dk, 21-jan-2012: 2.nd param was order no.
        $callString = AFSWS_CancelReservation(
            $this->userString(),
            AFSWS_CancelReservationObj($customerId, null, $amount)
        );
        // <<-- hf@bellcom.dk, 29-aug-2011: remove 2.nd param to CancelReservationObj, pr request of Gothia... don't know why, don't care why :)

        $response = $this->call('CancelReservation', $callString);

        $timer->logOne('cancelReservation, orderId #' . $order->getId());

        return $response;
    }

    /**
     * @param Customers $customer
     * @param Orders    $order
     * @param array     $additionalInfo
     *
     * @return GothiaApiCallResponse
     * @throws Exception
     * @throws GothiaApiCallException
     */
    public function checkCustomerAndPlaceReservation(Customers $customer, Orders $order, array $additionalInfo = null)
    {
        $hanzo         = Hanzo::getInstance();
        $domainKey     = str_replace('Sales', '', $hanzo->get('core.domain_key'));
        $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
        $customerId    = $customer->getId();
        $amount        = number_format($order->getTotalPrice(), 2, '.', '');
        $currencyCode  = $order->getCurrencyCode();

        if ($this->api->getTest()) {
            $customerId    = $this->getTestCustomerId($gothiaAccount->getSocialSecurityNum());
            $gothiaAccount = $customer->getGothiaAccounts(Propel::getConnection(null, Propel::CONNECTION_WRITE));
        }

        if (empty($customerId)) {
            Tools::debug('Missing customer id', __METHOD__);

            $customerId = $customer->getId();
        }

        $street = trim($order->getBillingAddressLine1() . ' ' . $order->getBillingAddressLine2());

        // nl hacks
        if ('NL' === $domainKey) {
            $pcs = preg_split('/ ([0-9]+)/', $street, 2, PREG_SPLIT_DELIM_CAPTURE);

            if (count($pcs) == 3) {
                $street = $pcs[0] . ' ' . $pcs[1] . str_replace(' ', '', $pcs[2]);
            } else {
                $street = implode('', $pcs);
            }
        }

        if (!$additionalInfo) {
            $additionalInfo = [
                'bank_account_no' => null,
                'bank_id'         => null,
                'payment_method'  => 'Invoice',
            ];
        }

        $callString = AFSWS_CheckCustomerAndPlaceReservation(
            $this->userString(),
            AFSWS_Customer(
                $street,
                $domainKey,
                $hanzo->get('core.currency'),
                $customerId,
                'Person',
                null,
                $gothiaAccount->getDistributionBy(),
                $gothiaAccount->getDistributionType(),
                $customer->getEmail(),
                null,
                $order->getBillingFirstName(),
                $order->getBillingLastName(),
                null,
                $gothiaAccount->getSocialSecurityNum(),
                $customer->getPhone(),
                str_replace(' ', '', $order->getBillingPostalCode()),
                $order->getBillingCity(),
                null
            ),
            AFSWS_Reservation('NoAccountOffer', $amount, $currencyCode, $customerId, null),
            AFSWS_AdditionalReservationInfo(
                $additionalInfo['bank_account_no'],
                $additionalInfo['bank_id'],
                $additionalInfo['payment_method']
            )
        );

        $response = $this->call('CheckCustomerAndPlaceReservation', $callString);

        return $response;
    }
}
