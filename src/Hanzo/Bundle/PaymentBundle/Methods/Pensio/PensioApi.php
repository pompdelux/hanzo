<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

use Exception;

use Hanzo\Model\Orders;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\Customers;

use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;
use Hanzo\Bundle\PaymentBundle\Methods\Pensio\PensioCallResponse;

use Symfony\Component\HttpFoundation\Request;

class PensioApi implements PaymentMethodApiInterface
{
    /**
     * undocumented class variable
     *
     * @var array
     */
    protected $settings = [
        'active'   => false,
        'checksum' => null,
        'fee'      => 0.00,
        'fee.id'   => null,
    ];

    /**
     * __construct
     *
     * @return void
     * @author Ulrik Nielsen <un@bellcom.dk>
     */
    public function __construct( $params, $settings )
    {
        foreach ($settings as $key => $value) {
            $this->settings[$key] = $value;
        }
        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);
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
     * updateOrderFailed
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @return void
     */
    public function updateOrderFailed( Request $request, Orders $order)
    {
        $order->setState( Orders::STATE_ERROR_PAYMENT );
        $order->setAttribute('paytype' , 'payment', 'Pensio');
        $order->save();
    }

    /**
     * updateOrderSuccess
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @return void
     */
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $order->setState( Orders::STATE_PAYMENT_OK );

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
        $order->save();
    }


    public function verifyCallback(Request $request, Orders $order)
    {
        if ($order->getState() != Orders::STATE_PRE_PAYMENT) {
            throw new InvalidOrderStateException('The order is not in the correct state "'. $order->getState() .'"');
        }

        if ('succeeded' !== $request->get('status')) {
            throw new PaymentFailedException('Payment failed: '.$request->get('error_message').' ('.$request->get('merchant_error_message').')');
        }


        if ($request->get('checksum') && $this->settings['checksum'])
//            'checksum',

        return true;
    }

    public function getProcessButton(Orders $order)
    {
        $language = LanguagesQuery::create()->select('iso2')->findOneById($order->getLanguagesId());

        $fields  = '<input type="hidden" name="terminal" value="'.$this->settings['terminal'].'">';
        $fields .= '<input type="hidden" name="shop_orderid" value="'.$order->getPaymentGatewayId().'">';
        $fields .= '<input type="hidden" name="amount" value="'.$order->getTotalPrice().'">';
        $fields .= '<input type="hidden" name="currency" value="'.$order->getCurrencyCode().'">';
        $fields .= '<input type="hidden" name="language" value="'.$language.'">';

        // extra info
        $fields .= '<input type="hidden" name="transaction_info[Firstname]" value="'.$order->getBillingFirstName().'">';
        $fields .= '<input type="hidden" name="transaction_info[Lastname]" value="'.$order->getBillingLastName().'">';
        $fields .= '<input type="hidden" name="transaction_info[Company]" value="'.$order->getBillingCompanyName().'">';
        $fields .= '<input type="hidden" name="transaction_info[Address1]" value="'.$order->getBillingAddressLine1().'">';
        $fields .= '<input type="hidden" name="transaction_info[Address2]" value="'.$order->getBillingAddressLine2().'">';
        $fields .= '<input type="hidden" name="transaction_info[City]" value="'.$order->getBillingCity().'">';
        $fields .= '<input type="hidden" name="transaction_info[Postalcode]" value="'.$order->getBillingPostalCode().'">';
        $fields .= '<input type="hidden" name="transaction_info[StateProvince]" value="'.$order->getBillingStateProvince().'">';
        $fields .= '<input type="hidden" name="transaction_info[Country]" value="'.$order->getBillingCountry().'">';
        $fields .= '<input type="hidden" name="transaction_info[Phone]" value="'.$order->getPhone().'">';
        $fields .= '<input type="hidden" name="transaction_info[Email]" value="'.$order->getEmail().'">';
        $fields .= '<input type="hidden" name="transaction_info[OrderId]" value="'.$order->getId().'">';

        return ['form' => '<form name="payment-dibs" id="payment-process-form" action="https://'.$this->settings['gateway'].'.pensio.com/" method="post" class="hidden">'.$fields.'</form>'];
    }
}
