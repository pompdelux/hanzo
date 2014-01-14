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

class DibsPaymentWindow extends BasePaymentApi implements PaymentMethodApiInterface
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
        'da_DK' => 'da_DK', // Danish
        'en_GB' => 'en_GB', // English
        'fi_FI' => 'en_GB', // Finnish
        'nl_NL' => 'en_GB', // Dutch
        'nb_NO' => 'nb_NO', // Norwegian
        'sv_SE' => 'sv_SE', // Swedish
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
     * __construct
     *
     * @param array $parameters
     * @param array $settings
     */
    public function __construct($parameters, array $settings)
    {
        $this->router = $parameters[0];

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
     * @throws Exception
     */
    public function checkSettings(array $settings)
    {
        $missing = [];
        foreach (['method_enabled', 'paytypes', 'merchant', 'test', 'md5key1', 'md5key2', 'mackey'] as $field) {
            if (!isset($settings[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new Exception( 'DibsApi: missing settings: '. implode(', ',$missing) );
        }
    }


    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * mergeSettings
     *
     * @param array $settings
     */
    public function mergeSettings(array $settings)
    {
        $this->settings = array_merge($this->settings,$settings);
    }

    /**
     * Get enabled Paytypes
     *
     * @return array
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
            ? $this->settings['active'] :
            false
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
     * @return boolean
     */
    public function getTest()
    {
        return (isset($this->settings['test']) && (strtoupper($this->settings['test']) == 'YES'))
            ? true
            : false
        ;
    }

    /**
     * verifyCallback
     *
     * @param Request $request
     * @param Orders $order The current order object
     * @throws Exception
     */
    public function verifyCallback(Request $request, Orders $order)
    {
        $mac = $request->request->get('MAC');
        $parameters = $request->request->all();

        ksort($parameters);

        $calculated = $this->calculateHmac($parameters);

        if ($mac !== $calculated) {
            throw new Exception( 'HMAC sum mismatch, got: "'.$mac.'" expected: "'.$calculated.'"' );
        }
    }

    /**
     * updateOrderSuccess
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @param  Request $request
     * @param  Orders  $order
     * @return int
     */
    public function updateOrderSuccess(Request $request, Orders $order)
    {
        return $this->updateOrderState(Orders::STATE_PAYMENT_OK, $request, $order);
    }

    /**
     * updateOrderFailed
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @param  Request $request
     * @param  Orders  $order
     * @return int
     */
    public function updateOrderFailed(Request $request, Orders $order)
    {
        return $this->updateOrderState(Orders::STATE_ERROR_PAYMENT, $request, $order);
    }

    /**
     * updateOrderState
     *
     * @param  integer $state   New order payment state
     * @param  Request $request Request object
     * @param  Orders  $order   Orders object
     * @return int
     */
    protected function updateOrderState($state, Request $request, Orders $order)
    {
        // renamed and synced
        $fields = [
            'status'                => 'status',
            'amount'                => 'amount',
            'currency'              => 'currency',
            'transaction'           => 'transact', // remapped to the old name, otherwise we need to rewrite most payment/sync flows
            'actionCode'            => 'action_code',
            'acquirer'              => 'acquirer',
            'cardNumberMasked'      => 'card_number_masked',
            'expMonth'              => 'exp_month',
            'expYear'               => 'exp_year',
            'cardTypeName'          => 'paytype',
            'captureStatus'         => 'capture_status',
            'status3D'              => 'status_3D',
            'ECI'                   => 'eci',
            'enrollStatus'          => 'enroll_status',
            'xidPresent'            => 'xid_present',
            'ticket'                => 'ticket',
            'ticketStatus'          => 'ticket_status',
            'fee'                   => 'fee',
            'verificationIdPresent' => 'verification_id_present',
            'validationErrors'      => 'validation_errors'
        ];

        foreach ($fields as $field => $name) {
            $value = $request->request->get($field);

            if (empty($value)) {
                continue;
            }

            $order->setAttribute($name , 'payment', $request->request->get($field));
        }

        $order->setState($state);
        return $order->save();
    }

    /**
     * call
     *
     * @return DibsApiCall
     */
    public function call()
    {
        return DibsApiCall::getInstance($this->settings, $this);
    }


    /**
     * md5keyFromString
     *
     * @param string $string containing the key=value pairs to be hashed
     * @return string
     */
    public function md5keyFromString($string)
    {
        return md5($this->settings['md5key2'].md5($this->settings['md5key1'].$string));
    }


    /**
     * Calculate HMAC sum
     *
     * @param  array $parameters
     * @return string
     */
    protected function calculateHmac(array $parameters)
    {
        if (isset($parameters['MAC'])) {
            unset($parameters['MAC']);
        }

        $query = '';
        foreach ($parameters as $key => $value) {
            $query .= $key.'='.$value.'&';
        }
        $query = rtrim($query, '&');

        return hash_hmac("sha256", $query, $this->hmacKey());
    }

    protected function hmacKey()
    {
        $string = "";
        foreach (explode("\n", trim(chunk_split($this->settings['mackey'], 2))) as $h) {
            $string .= chr(hexdec($h));
        }

        return $string;
    }

    /**
     * buildFormFields
     * @param Orders $order
     * @return array
     */
    public function buildFormFields(Orders $order)
    {
        $orderId  = $order->getPaymentGatewayId();
        $amount   = self::formatAmount( $order->getTotalPrice() );
        $currency = $this->currencyCodeToNum($order->getCurrencyCode());

        $locale = Hanzo::getInstance()->get('core.locale');
        $lang   = isset($this->language_map[$locale])
            ? $this->language_map[$locale]
            : 'en'
        ;

        $settings = [
            'orderId'         => $orderId,
            'amount'          => $amount,
            'language'        => $lang,
            "merchant"        => $this->getMerchant(),
            "currency"        => $currency,
            "cancelReturnUrl" => $this->router->generate('PaymentBundle_dibs_cancel',   [], true),
            "callbackUrl"     => $this->router->generate('PaymentBundle_dibs_callback', [], true),
            "acceptReturnUrl" => $this->router->generate('PaymentBundle_dibs_process',  ['order_id' => $orderId], true),
        ];

        // Only send these fields, to many fields result in hitting a post limit or something
        $settings['billingFirstName']   = $order->getBillingFirstName();
        $settings['billingLastName']    = $order->getBillingLastName();
        $settings['billingAddress']     = $order->getBillingAddressLine1();
        $settings['billingAddress2']    = $order->getBillingAddressLine2();
        $settings['billingPostalPlace'] = $order->getBillingCity();
        $settings['billingPostalCode']  = $order->getBillingPostalCode();
        $settings['billingEmail']       = $order->getEmail();
        $settings['billingMobile']      = $order->getPhone();

        $settings['s_telephone'] = $order->getPhone();
        $settings['s_email']     = $order->getEmail();
        $settings['s_orderid']   = $order->getId();

        if ($this->getTest()) {
            $settings['test'] = 1;
        }

        // must be done right before hmac calculation !!
        ksort($settings);

        $settings['MAC'] = $this->calculateHmac($settings);

        return $settings;
    }

    /**
     * formatAmount
     *
     * @param  float $amount
     * @return float
     */
    public static function formatAmount($amount)
    {
        return (number_format($amount, 2, '.', '')) * 100;
    }

    /**
     * currencyCodeToNum
     * Should at some point maybe use the currency_id set in the counties model
     *
     * @param  string $code
     * @return int
     * @throws Exception
     */
    public function currencyCodeToNum($code)
    {
        if (isset($this->currency_map[$code])) {
            return $this->currency_map[$code];
        }

        throw new Exception('DibsApi: unknown currency code: '.$code);
    }


    /**
     * Return process button to the checkout builder.
     *
     * @param  Orders  $order Ordes object
     * @param  Request $request
     * @return string
     */
    public function getProcessButton(Orders $order, Request $request)
    {
        $fields = '';
        foreach ($this->buildFormFields($order) as $name => $value) {
            $fields .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
        }

        return ['form' => '<form name="payment-dibs" id="payment-process-form" action="https://sat1.dibspayment.com/dibspaymentwindow/entrypoint" method="post" class="hidden">'.$fields.'</form>'];
    }
}
