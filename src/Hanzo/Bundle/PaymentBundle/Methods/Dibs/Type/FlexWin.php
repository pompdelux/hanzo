<?php

namespace Hanzo\Bundle\PaymentBundle\Methods\Dibs\Type;

use Exception;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;

use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;
use Hanzo\Bundle\PaymentBundle\BasePaymentApi;
use Hanzo\Bundle\PaymentBundle\Methods\Dibs\DibsApiCall;
use Hanzo\Bundle\PaymentBundle\Methods\Dibs\DibsApiCallException;

use Symfony\Component\HttpFoundation\Request;

class FlexWin extends BasePaymentApi implements PaymentMethodApiInterface
{
    /**
     * map currencies to dibs currency codes
     *
     * @var array
     */
    protected $currency_map = [
        'DKK' => 208,
        'EUR' => 978,
        'USD' => 840,
        'GBP' => 826,
        'SEK' => 752,
        'AUD' => 036,
        'CAD' => 124,
        'ISK' => 352,
        'JPY' => 392,
        'NZD' => 554,
        'NOK' => 578,
        'CHF' => 756,
        'TRY' => 949,
    ];

    /**
     * Maps locales to dibs languages
     *
     * @var array
     */
    protected $language_map = [
        'da_DK' => 'da', // Danish
        'en_GB' => 'en', // English
        'fi_FI' => 'fi', // Finnish
        'nl_NL' => 'nl', //Dutch
        'nb_NO' => 'no', //Norwegian
        'sv_SE' => 'sv', // Swedish
        'de_DE' => 'de', //German
        'de_AT' => 'de', //German
        'de_CH' => 'de', //German
    ];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var Router
     */
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
     */
    public function __construct($parameters, array $settings)
    {
        $this->router         = $parameters[0];
        $this->service_logger = $parameters[1];

        $this->settings = $settings;
        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled']
            ? true
            : false
        );

        if ($this->settings['active'] === true) {
            $this->checkSettings($settings);
        }

        if (isset($settings['paytypes'])) {
            $this->settings['paytypes'] = unserialize($settings['paytypes']);
        }
    }

    /**
     * checkSettings
     *
     * @param array $settings
     * @throws \Exception
     */
    public function checkSettings(array $settings)
    {
        $requiredFields = [
            'method_enabled',
            'paytypes',
            'merchant',
            'test',
            'md5key1',
            'md5key2',
        ];

        $missing = [];

        foreach ($requiredFields as $field) {
            if (!isset($settings[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new Exception( 'DibsApi: missing settings: '. implode(',',$missing) );
        }
    }

    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * mergeSettings
     *
     * @param array $settings
     * @return void
     */
    public function mergeSettings(array $settings)
    {
        $this->settings = array_merge($this->settings,$settings);
    }

    /**
     * getPayTypes
     *
     * @return mixed
     */
    public function getPayTypes()
    {
        return $this->settings['paytypes'];
    }

    /**
     * isActive
     * Checks if the api is active for the current configuration
     *
     * @return bool
     */
    public function isActive()
    {
        return isset($this->settings['active'])
            ? $this->settings['active']
            : false
        ;
    }

    /**
     * getFeeExternalId
     *
     * @return mixed
     */
    public function getFeeExternalId()
    {
        return isset($this->settings['fee.id'])
            ? $this->settings['fee.id']
            : null
        ;
    }

    /**
     * getMerchant
     *
     * @return string
     */
    public function getMerchant()
    {
        return $this->settings['merchant'];
    }

    /**
     * getTest
     *
     * @return boolean
     */
    public function getTest()
    {
        return (isset($this->settings['test']) && strtoupper($this->settings['test']) == 'YES')
            ? true
            : false
        ;
    }

    /**
     * verifyCallback
     *
     * @param Request $callbackRequest
     * @param Orders $order The current order object
     * @throws \Exception
     */
    public function verifyCallback(Request $callbackRequest, Orders $order)
    {
        // The order must be in the pre payment state, if not it has not followed the correct flow
        if ($order->getState() != Orders::STATE_PRE_PAYMENT) {
            $msg = 'The order is not in the correct state "'. $order->getState() .'"';
            Tools::debug($msg, __METHOD__, ['POST' => $_POST]);
            throw new Exception( $msg );
        }

        if ($callbackRequest->request->get('merchant') != $this->settings['merchant']) {
            throw new Exception('Wrong merchant "'. $callbackRequest->request->get('merchant') .'"');
        }

        $currency   = $this->currencyCodeToNum($order->getCurrencyCode());
        $amount     = self::formatAmount($order->getTotalPrice());
        $gateway_id = $order->getPaymentGatewayId();
        $calculated = $this->md5key($gateway_id, $currency, $amount);

        if ($callbackRequest->request->get('md5key') != $calculated) {
            Tools::debug( 'Md5 sum mismatch', __METHOD__, [
                'POST'              => $_POST,
                'GatewayID'         => $gateway_id,
                'Amount'            => $amount,
                'md5 Calculated'    => $calculated,
                'md5 From callback' => $callbackRequest->request->get('md5key'),
            ]);

            throw new Exception('Md5 sum mismatch, got: "'. $callbackRequest->request->get('md5key') .'" expected: "'. $calculated .'"');
        }

        $calculated = $this->md5AuthKey($callbackRequest->request->get('transact'), $currency, $amount);

        if ($callbackRequest->request->get('authkey') != $calculated) {
            Tools::debug( 'Authkey md5 sum mismatch', __METHOD__, [
                'POST'              => $_POST,
                'GatewayID'         => $gateway_id,
                'Amount'            => $amount,
                'md5 Calculated'    => $calculated,
                'md5 From callback' => $callbackRequest->request->get('authkey'),
            ]);

            throw new Exception('Authkey md5 sum mismatch, got: "'. $callbackRequest->request->get('authkey') .'" expected: "'. $calculated .'"');
        }
    }

    /**
     * updateOrderSuccess
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @param Request $request
     * @param Orders  $order
     */
    public function updateOrderSuccess(Request $request, Orders $order)
    {
        $fields = [
            'paytype',
            'cardnomask',
            'cardprefix',
            'acquirer',
            'cardexpdate',
            'currency',
            'ip',
            'approvalcode',
            'transact',
        ];

        foreach ($fields as $field) {
            $order->setAttribute($field , 'payment', $request->get($field));
        }

        $order->setState(Orders::STATE_PAYMENT_OK);
        $order->save();
    }

    /**
     * updateOrderFailed
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @param Request $request
     * @param Orders  $order
     */
    public function updateOrderFailed(Request $request, Orders $order)
    {
        $fields = array(
            'paytype',
            'cardnomask',
            'cardprefix',
            'acquirer',
            'cardexpdate',
            'currency',
            'ip',
            'approvalcode',
            'transact',
        );

        foreach ($fields as $field) {
            $order->setAttribute($field , 'payment', $request->get($field));
        }

        $order->setState( Orders::STATE_ERROR_PAYMENT );
        $order->save();
    }

    /**
     * call
     * @return DibsApiCall
     */
    public function call()
    {
        return DibsApiCall::getInstance($this->settings, $this);
    }

    /**
     * Calculate md5 sum for verification
     * @param int $orderId
     * @param int $currency
     * @param int $amount
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function md5key($orderId, $currency, $amount)
    {
        return $this->md5keyFromString( 'merchant='. $this->settings['merchant'] .'&orderid='. $orderId .'&currency='.$currency.'&amount='.$amount);
    }

    /**
     * md5AuthKey
     * @param int $transact
     * @param int $currency
     * @param int $amount
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function md5AuthKey($transact, $currency, $amount)
    {
        return $this->md5keyFromString( 'transact='.$transact.'&amount='.$amount.'&currency='.$currency );
    }

    /**
     * md5keyFromString
     * @param string $string containing the key=value pairs to be hashed
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function md5keyFromString($string)
    {
        return md5( $this->settings['md5key2'] . md5( $this->settings['md5key1'] . $string));
    }

    /**
     * formatAmount
     *
     * @param float $amount
     * @return float
     */
    public static function formatAmount($amount)
    {
        $amount = (number_format($amount, 2, '.', '')) * 100 ;
        return $amount;
    }

    /**
     * buildFormFields
     * @param Orders $order
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function buildFormFields(Orders $order)
    {
        $orderId  = $order->getPaymentGatewayId();
        $amount   = self::formatAmount( $order->getTotalPrice() );
        $currency = $this->currencyCodeToNum($order->getCurrencyCode());

        $locale = Hanzo::getInstance()->get('core.locale');
        $lang = (isset($this->language_map[$locale])
            ? $this->language_map[$locale]
            : 'en'
        );

        $settings = [
            'orderid'     => $orderId,
            'amount'      => $amount,
            'lang'        => $lang,
            "merchant"    => $this->getMerchant(),
            "currency"    => $currency,
            "cancelurl"   => $this->router->generate('PaymentBundle_dibs_cancel', [], true),
            "callbackurl" => $this->router->generate('PaymentBundle_dibs_callback', [], true),
            "accepturl"   => $this->router->generate('PaymentBundle_dibs_process', ['order_id' => $orderId], true),
            "uniqueoid"   => "YES",
            "paytype"     => $order->getAttributes()->payment->paytype,
            "md5key"      => $this->md5key($orderId, $currency, $amount),
        ];

        // shortcut payment for iDeal:ABN payments (nl)
        if ($order->getAttributes()->payment->paytype == 'ABN') {
            $settings['decorator'] = 'rich';
        }

        // Only send these fields, to many fields result in hitting a post limit or something
        $settings['delivery01.Firstname']     = $order->getBillingFirstName();
        $settings['delivery02.Lastname']      = $order->getBillingLastName();
        $settings['delivery03.Company']       = $order->getBillingCompanyName();
        $settings['delivery04.Address1']      = $order->getBillingAddressLine1();
        $settings['delivery05.Address2']      = $order->getBillingAddressLine2();
        $settings['delivery06.City']          = $order->getBillingCity();
        $settings['delivery07.Postcode']      = $order->getBillingPostalCode();
        $settings['delivery08.StateProvince'] = $order->getBillingStateProvince();
        $settings['delivery09.Country']       = $order->getBillingCountry();
        $settings['delivery10.Telephone']     = $order->getPhone();
        $settings['delivery11.Email']         = $order->getEmail();
        $settings['delivery12.OrderId']       = $order->getId();

        if ($this->getTest()) {
            $settings["test"] = 'YES';
        }

        return $settings;
    }

    /**
     * currencyCodeToNum
     * Should at some point maybe use the currency_id set in the counties model
     *
     * @param string $code
     * @throws \Exception
     * @return int
     */
    public function currencyCodeToNum($code)
    {
        if (isset($this->currency_map[$code])) {
            return $this->currency_map[$code];
        }

        throw new Exception('DibsApi: unknown currency code: '.$code);
    }


    /**
     * Build the process button for the payment flow
     *
     * @param Orders  $order
     * @param Request $request
     * @return array
     */
    public function getProcessButton(Orders $order, Request $request)
    {
        $fields = '';
        $data = $this->buildFormFields($order);

        $this->service_logger->plog($data, ['outgoing', 'payment', 'dibs', 'flex-win',  'payment-form']);

        foreach ($data as $name => $value) {
            $fields .= '<input type="hidden" name="'.$name.'" value="'.$value.'" >';
        }

        return ['form' => '<form name="payment-dibs" id="payment-process-form" action="https://payment.architrade.com/paymentweb/start.action" method="post" class="hidden">'.$fields.'</form>'];
    }
}
