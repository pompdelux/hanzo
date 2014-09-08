<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out\Services;

/**
 * Class SyncInventory
 * @package Hanzo\Bundle\AxBundle
 */
class SyncInventory extends BaseService
{
    /**
     * @return \stdClass
     */
    public function get()
    {
        $this->data = (object) [
            'endpointDomain' => $this->getEndPoint()
        ];

        return $this->data;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function send($name = null)
    {
        ini_set("default_socket_timeout", 600);
        return parent::send();
    }
}
