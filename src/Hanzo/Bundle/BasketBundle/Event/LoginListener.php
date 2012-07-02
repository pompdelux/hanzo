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
        $order = OrdersPeer::getCurrent();

        if ($order->getTotalPrice(true)) {
            $order->recalculate();
            $order->save();
        }

    }
}
