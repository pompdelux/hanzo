<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AccountBundle\Event;

use Hanzo\Model\AddressesQuery;
use Hanzo\Model\GothiaAccountsQuery;
use Hanzo\Model\OrdersPeer;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class LoginListener
 *
 * @package Hanzo\Bundle\AccountBundle\Event
 */
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
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $order = OrdersPeer::getCurrent();

        if ($order->getTotalPrice(true)) {
            $order->recalculate();
            $order->save();
        }

        /** @var \Hanzo\Model\Customers $user */
        $user = $event->getAuthenticationToken()->getUser();

        // Reset "may be contacted" to false
        $user->setMayBeContacted(false);
        $user->save();

        // Delete any attached shipping addresses.
        AddressesQuery::create()
            ->filterByType('shipping')
            ->filterByCustomersId($user->getId())
            ->delete();

        // Delete any attached Avarto/Gothia account info
        GothiaAccountsQuery::create()
            ->filterByCustomersId($user->getId())
            ->delete();
    }
}
