<?php
/**
 * Created by PhpStorm.
 * User: un
 * Date: 14/01/14
 * Time: 09.52
 */

namespace Hanzo\Bundle\ConsignorBundle;


class Consignor
{
    private $consignor;
    private $logger;
    private $options;

    public function __construct($guzzle_client, $logger)
    {
        print_r(get_class($guzzle_client));
        print_r(get_class($logger));

        $this->consignor = $guzzle_client;
        $this->logger    = $logger;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
