<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AccountBundle\Handler;

use Hanzo\Bundle\ServiceBundle\Services\MailService;
use Hanzo\Model\CustomersPeer;
use Symfony\Component\Routing\Router;

/**
 * Class SendWishlistHandler
 *
 * @package Hanzo\Bundle\AccountBundle
 */
class SendWishlistMailHandler
{
    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param MailService $mailService
     * @param Router      $router
     */
    public function __construct(MailService $mailService, Router $router)
    {
        $this->mailService = $mailService;
        $this->router      = $router;
    }

    /**
     * @param string $toAddress Email to send the message to.
     * @param string $listId    Id/code of the list.
     *
     * @return int
     */
    public function send($toAddress, $listId)
    {
        $customer = CustomersPeer::getCurrent();

        $this->mailService->setTo($toAddress);
        $this->mailService->setMessage('wishlist', [
            'listId' => $listId,
            'owner'  => $customer->getName(),
            'date'   => date('d/m-Y'),
            'link'   => $this->router->generate('_account_wishlist_load', ['listId' => $listId], true),
        ]);

        $this->mailService->setFrom(['events@pompdelux.com' => $customer->getName() . ' (via POMPdeLUX)']);
        $this->mailService->setSender('events@pompdelux.com', 'POMPdeLUX', true);
        $this->mailService->setReplyTo($customer->getEmail(), $customer->getName());

        return $this->mailService->send();
    }
}
