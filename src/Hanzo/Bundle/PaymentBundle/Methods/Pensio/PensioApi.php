<?php /* vim: set sw=4: */

/*

INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('DK', 'pensioapi', 'method_enabled', '1');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('DK', 'pensioapi', 'gateway', 'testgateway');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('DK', 'pensioapi', 'terminal', 'Pomp De Lux iDEAL Test Terminal');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('DK', 'pensioapi', 'secret', '');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('DK', 'pensioapi', 'api_user', 'un@bellcom.dk');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('DK', 'pensioapi', 'api_pass', 'y2etx3@vz5Jc');

INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('NL', 'pensioapi', 'method_enabled', '1');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('NL', 'pensioapi', 'gateway', 'testgateway');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('NL', 'pensioapi', 'terminal', 'Pomp De Lux iDEAL Test Terminal');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('NL', 'pensioapi', 'secret', '');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('NL', 'pensioapi', 'api_user', 'un@bellcom.dk');
INSERT INTO domains_settings (domain_key, ns, c_key, c_value) VALUES ('NL', 'pensioapi', 'api_pass', 'y2etx3@vz5Jc');

*/

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

use Exception;
use SimpleXMLElement;

use Hanzo\Model\Orders;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\Customers;

use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;
use Hanzo\Bundle\PaymentBundle\Methods\Pensio\PensioCallResponse;

use Symfony\Component\HttpFoundation\Request;

class PensioApi implements PaymentMethodApiInterface
{
    /**
     * api settings
     *
     * @var array
     */
    protected $settings = [
        'active'   => false,
        'gateway'  => 'testgateway',
        'terminal' => '',
        'secret'   => null,
        'fee'      => 0.00,
        'fee.id'   => null,

        'api_user' => '',
        'api_pass' => '',
    ];

    protected $router;

    /**
     * __construct
     *
     * @return void
     * @author Ulrik Nielsen <un@bellcom.dk>
     */
    public function __construct( $params, $settings )
    {
        $this->router = $parameters[0];

        foreach ($settings as $key => $value) {
            $this->settings[$key] = $value;
        }

        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);

        // without gateway and terminal we cannot enable the module.
        if ($this->settings['active'] && (!$this->settings['terminal'] || !$this->settings['gateway'])) {
            $this->settings['active'] = false;
        }
    }

    /**
     * call
     * Dummy implementation as this method does not use an api call
     */
    public function call()
    {
        return $this;
    }

    /**
     * cancel
     *
     * @return PensioCallResponse
     */
    public function cancel(Customers $customer, Orders $order)
    {
        return new PensioCallResponse();
    }

    /**
     * isActive
     * Checks if the api is active for the current configuration
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->settings['active'];
    }

    /**
     * getFee
     * @return float
     */
    public function getFee()
    {
        return $this->settings['fee'];
    }

    /**
     * getFeeExternalId
     * @return void
     */
    public function getFeeExternalId()
    {
        return $this->settings['fee.id'];
    }

    /**
     * these must be here or the interface get's angry...
     * @param Request $request
     * @param Orders  $order
     */
    public function updateOrderSuccess(Request $request, Orders $order)
    {
        $this->updateOrderStatus(Orders::STATE_PAYMENT_OK, $request, $order);
    }
    public function updateOrderFailed(Request $request, Orders $order)
    {
        $this->updateOrderStatus(Orders::STATE_ERROR_PAYMENT, $request, $order);
    }


    /**
     * set order status and attributes based on pament status
     *
     * @param Integer $status  Status id
     * @param Request $request
     * @param Orders  $order
     */
    public function updateOrderStatus($status, Request $request, Orders $order)
    {
        $params = [
            'status',
            'error_message',
            'merchant_error_message',
            'shop_orderid',
            'transaction_id',
            'type',
            'payment_status',
            'masked_credit_card',
            'blacklist_token',
            'credit_card_token',
            'nature',
            'require_capture',
        ];

        foreach ($params as $key) {
            $order->setAttribute($key, 'payment', $request->get($key));
        }

        $order->setAttribute('paytype', 'payment', 'Pensio');
        $order->setState($status);
        $order->save();
    }


    /**
     * validate the callback before processing the order.
     *
     * @param Request $request
     * @param Orders  $order
     */
    public function verifyCallback(Request $request, Orders $order)
    {
        if ($order->getState() != Orders::STATE_PRE_PAYMENT) {
            throw new InvalidOrderStateException('The order is not in the correct state "'. $order->getState() .'"');
        }

        if ('succeeded' !== $_POST['status']) {
            throw new PaymentFailedException('Payment failed: '.$request->get('error_message').' ('.$request->get('merchant_error_message').')');
        }


        if ($request->get('checksum') && $this->settings['secret']) {
            $md5 = md5($order->getTotalPrice().$order->getCurrencyCode().$order->getPaymentGatewayId().$this->settings['secret']);
            if (0 !== strcmp($md5, $request->get('checksum'))) {
                throw new Exception('Payment failed: checksum mismatch');
            }
        }
    }


    /**
     * Build and return the form used in the checkout flow.
     *
     * @param  Orders $order The order object
     * @return string The form used to proceed to the Pensio payment window
     */
    public function getProcessButton(Orders $order)
    {
        $language = LanguagesQuery::create()->select('iso2')->findOneById($order->getLanguagesId());

        $cookie = [];
        foreach ($_COOKIE as $key => $value) {
            $cookie[] = $key.'='.rawurlencode($value);
        }
        $cookie = implode('; ', $cookie);

        $data = [
            'terminal' => $this->settings['terminal'],
            'shop_orderid' => $order->getPaymentGatewayId(),
            'amount' => number_format($order->getTotalPrice(), 2, '.', ''),
            'currency' => $order->getCurrencyCode(),
            'language' => $language,
            'config' => [
                'callback_form' => $this->router->generate('_pensio_form', [], true),
                'callback_redirect' => $this->router->generate('_pensio_wait', [], true),
                'callback_ok' => $this->router->generate('_pensio_callback', ['status' => 'ok'], true),
                'callback_fail' => $this->router->generate('_pensio_callback', ['status' => 'failed'], true),
            ],
            'transaction_info' => [
                'Firstname' => $order->getBillingFirstName(),
                'Lastname' => $order->getBillingLastName(),
                'Company' => $order->getBillingCompanyName(),
                'Address1' => $order->getBillingAddressLine1(),
                'Address2' => $order->getBillingAddressLine2(),
                'City' => $order->getBillingCity(),
                'Postalcode' => $order->getBillingPostalCode(),
                'StateProvince' => $order->getBillingStateProvince(),
                'Country' => $order->getBillingCountry(),
                'Phone' => $order->getPhone(),
                'Email' => $order->getEmail(),
                'OrderId' => $order->getId(),
            ],
            'cookie' => $cookie,
        ];
Tools::log($data);
        $headers = [
            'Authorization: Basic '.base64_encode($this->settings['api_user'].':'.$this->settings['api_pass']),
            'Content-type: application/x-www-form-urlencoded; charset=utf-8',
        ];

        $request = array(
            'http' => array(
                'header' => implode("\r\n", $headers),
                'method' => 'POST',
                'max_redirects' => 0,
                'timeout' => 5,
                'ignore_errors' => false,
                'content' => http_build_query($data),
            )
        );
        $context = stream_context_create($request);
        $response = trim(file_get_contents('https://testgateway.pensio.com/merchant/API/createPaymentRequest', FALSE, $context));

        $goto = 'payment/cancel';
        if ($response && ($xml = new SimpleXMLElement($response))) {
            $code = (string) $xml->Header->ErrorCode;
            if (0 == $code) {
                $goto = (string) $xml->Body->Url;
            }
        }

        return ['url' => $goto];
    }
}
