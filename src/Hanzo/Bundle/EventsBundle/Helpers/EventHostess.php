<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\EventsBundle\Helpers;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Addresses;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Events;

/**
 * Class EventHostess
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class EventHostess
{
    /**
     * @var Events
     */
    private $event;

    /**
     * @param Events $event
     */
    public function __construct(Events $event)
    {
        $this->event = $event;
    }

    /**
     * Find existing or create ned hostess (customer) record.
     *
     * @return Customers
     */
    public function getHostess()
    {
        $host = CustomersQuery::create()
            ->findOneByEmail($this->event->getEmail());

        if (!$host instanceof Customers) {
            @list($first, $last) = explode(' ', $this->event->getHost(), 2);

            $host = new Customers();
            $host->setPasswordClear($this->event->getPhone());
            $host->setPassword(sha1($this->event->getPhone()));
            $host->setPhone($this->event->getPhone());
            $host->setEmail($this->event->getEmail());
            $host->setFirstName($first);
            $host->setLastName($last);

            try {
                $host->save();

                $country = CountriesQuery::create()
                    ->findOneByIso2(Hanzo::getInstance()->get('core.country'));

                // create customer payment address
                $address = new Addresses();
                $address->setType('payment');
                $address->setCustomersId($host->getId());
                $address->setFirstName($first);
                $address->setLastName($last);
                $address->setAddressLine1($this->event->getAddressLine1());
                $address->setPostalCode($this->event->getPostalCode());
                $address->setCity($this->event->getCity());
                $address->setCountry($country->getName());
                $address->setCountriesId($country->getId());
                $address->save();

            } catch (\PropelException $e) {
                Tools::log($this->event->toArray());
            }
        }

        return $host;
    }
}
