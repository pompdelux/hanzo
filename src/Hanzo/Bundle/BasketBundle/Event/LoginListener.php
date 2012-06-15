<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\BasketBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\SecurityContext;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\OrdersPeer;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;

use \Criteria;
use \PropelCollection;

class LoginListener
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $context;

    /**
     * Constructor
     *
     * @param SecurityContext $context
     */
    public function __construct(SecurityContext $context)
    {
        $this->context = $context;
    }

    /**
     * Recalculate basket if nessesary.
     *
     * @param  Event $event
     */
    public function onSecurityInteractiveLogin(Event $event)
    {
        $hanzo = Hanzo::getInstance();
        $order = OrdersPeer::getCurrent();

        $customer = $this->context->getToken()->getUser()->getUser();

        // add billing address to order
        $c = new Criteria;
        $c->add(AddressesPeer::TYPE, 'payment');
        $order->setBillingAddress($customer->getAddressess($c)->getFirst());

        if ($order->getTotalPrice(true) && ('COM' == $hanzo->get('core.domain_key'))) {
            $country = $order->getCountriesRelatedByBillingCountriesId();
            if ($country->getVat()) {
                return;
            }

            $lines = $order->getOrdersLiness();
            $collection = new PropelCollection();

            $product_ids = array();
            foreach ($lines as $line) {
                if('product' == $line->getType()) {
                    $product_ids[] = $line->getProductsId();
                }
            }

            $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);

            foreach ($lines as $line) {
                if('product' == $line->getType()) {
                    $price = $prices[$line->getProductsId()];

                    $sales = $price['normal'];
                    if (isset($price['sales'])) {
                        $sales = $price['sales'];
                    }

                    $line->setPrice($sales['price']);
                    $line->setVat(0);
                    $line->setOriginalPrice($price['normal']['price']);
                }

                $collection->prepend($line);
            }

            $order->setOrdersLiness($collection);
        }

        $order->save();
    }
}
