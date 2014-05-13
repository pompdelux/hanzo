<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

use Exception;
use SimpleXMLElement;

use Hanzo\Model\Orders;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\Customers;

use Hanzo\Bundle\PaymentBundle\BasePaymentApi;
use Hanzo\Bundle\PaymentBundle\Methods\Pensio\PensioCallResponse;

use Symfony\Component\HttpFoundation\Request;

class PensioApi extends BasePaymentApi
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
     * @var \Hanzo\Core\ServiceLogger
     */
    public $service_logger;

    /**
     * __construct
     *
     * @param array $parameters
     * @param array $settings
     * @author Ulrik Nielsen <un@bellcom.dk>
     */
    public function __construct($parameters, $settings)
    {
        $this->router         = $parameters[0];
        $this->service_logger = $parameters[1];

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
     *
     * @return PensioMerchantApi
     */
    public function call()
    {
        return PensioMerchantApi::getInstance($this->settings, $this->service_logger);
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
            $order->setAttribute($key, 'payment', $request->request->get($key));
        }

        // map out extra information found in the attached xml document - more might be needed
        $xml = simplexml_load_string(urldecode($request->request->get('xml')), 'SimpleXMLElement', LIBXML_NOCDATA);
        if (isset($xml->Body->Transactions->Transaction->PaymentNatureService) ) {
            $map = [
                'false' => 0,
                'true'  => 1,
            ];

            foreach ($xml->Body->Transactions->Transaction->PaymentNatureService->children() as $child) {
                $order->setAttribute($child->getName(), 'payment', $map[(string) $child]);
            }
        }

        $order->setAttribute('paytype', 'payment', 'Pensio');
        $order->setState($status);
        $order->save();
    }


    /**
     * validate the callback before processing the order.
     *
     * @param  Request $request
     * @param  Orders  $order
     * @throws InvalidOrderStateException
     * @throws PaymentFailedException
     * @throws Exception
     */
    public function verifyCallback(Request $request, Orders $order)
    {
        if ($order->getState() != Orders::STATE_PRE_PAYMENT) {
            throw new InvalidOrderStateException('The order is not in the correct state "'. $order->getState() .'"');
        }

        if ('succeeded' !== $_POST['status']) {
            throw new PaymentFailedException('Payment failed: '.$request->request->get('error_message').' ('.$request->request->get('merchant_error_message').')');
        }

        if ($request->request->get('checksum') && $this->settings['secret']) {
            $md5 = md5($order->getTotalPrice().$order->getCurrencyCode().$order->getPaymentGatewayId().$this->settings['secret']);
            if (0 !== strcmp($md5, $request->request->get('checksum'))) {
                throw new Exception('Payment failed: checksum mismatch');
            }
        }
    }


    /**
     * Build and return the form used in the checkout flow.
     *
     * @param  Orders  $order The order object
     * @param  Request $request
     * @return string The form used to proceed to the Pensio payment window
     */
    public function getProcessButton(Orders $order, Request $request)
    {
        $language = LanguagesQuery::create()
            ->select('iso2')
            ->findOneById($order->getLanguagesId()
        );

        $cookie = [];
        foreach ($_COOKIE as $key => $value) {
            $cookie[] = $key.'='.rawurlencode($value);
        }
        $cookie = implode('; ', $cookie);

        $data = [
            'terminal'     => $this->settings['terminal'],
            'shop_orderid' => $order->getPaymentGatewayId(),
            'amount'       => number_format($order->getTotalPrice(), 2, '.', ''),
            'currency'     => $order->getCurrencyCode(),
            'language'     => $language,
            'config'       => [
                'callback_fail'         => $this->router->generate('_pensio_callback', ['status' => 'failed'], true),
                'callback_form'         => $this->router->generate('_pensio_form', [], true),
                'callback_notification' => $this->router->generate('_pensio_callback', ['status' => 'ok'], true),
                'callback_ok'           => $this->router->generate('_pensio_callback', ['status' => 'ok'], true),
                'callback_redirect'     => $this->router->generate('_pensio_wait', [], true),
            ],
            'transaction_info' => [
                'Firstname'     => $order->getBillingFirstName(),
                'Lastname'      => $order->getBillingLastName(),
                'Company'       => $order->getBillingCompanyName(),
                'Address1'      => $order->getBillingAddressLine1(),
                'Address2'      => $order->getBillingAddressLine2(),
                'City'          => $order->getBillingCity(),
                'Postalcode'    => $order->getBillingPostalCode(),
                'StateProvince' => $order->getBillingStateProvince(),
                'Country'       => $order->getBillingCountry(),
                'Phone'         => $order->getPhone(),
                'Email'         => $order->getEmail(),
                'OrderId'       => $order->getId(),
            ],
            'cookie' => $cookie,
        ];

        $headers = [
            'Authorization: Basic '.base64_encode($this->settings['api_user'].':'.$this->settings['api_pass']),
            'Content-type: application/x-www-form-urlencoded; charset=utf-8',
        ];

        $request = ['http' => [
            'header'        => implode("\r\n", $headers),
            'method'        => 'POST',
            'max_redirects' => 0,
            'timeout'       => 5,
            'ignore_errors' => false,
            'content'       => http_build_query($data),
        ]];

        $this->service_logger->plog(array_merge($data, $headers), ['outgoing', 'payment', 'pensio', 'createPaymentRequest']);

        $context = stream_context_create($request);
        $response = trim(file_get_contents('https://'.$this->settings['gateway'].'.pensio.com/merchant/API/createPaymentRequest', FALSE, $context));

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
