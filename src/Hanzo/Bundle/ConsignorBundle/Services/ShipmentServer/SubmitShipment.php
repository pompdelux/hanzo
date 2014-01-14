<?php
/**
 * Created by PhpStorm.
 * User: un
 * Date: 14/01/14
 * Time: 11.02
 */

namespace Hanzo\Bundle\ConsignorBundle\Services\ShipmentServer;

use Hanzo\Bundle\ConsignorBundle\Consignor;
use Hanzo\Bundle\ConsignorBundle\ConsignorAddress;

class SubmitShipment
{
    private $consignor;

    public function __construct(Consignor $consignor)
    {
        $this->consignor = $consignor;
    }

    public function setFromAddress(ConsignorAddress $address) {}
    public function setToAddress(ConsignorAddress $address) {}
}
