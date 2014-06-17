<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\ProductBundle;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\Settings;
use Hanzo\Model\SettingsQuery;
use Symfony\Component\HttpFoundation\Session\Session;

class Range
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @var string
     */
    private $active_range;

    /**
     * @var array
     */
    private $available_ranges = [];


    /**
     * Construct
     *
     * @param Session $session
     * @param string  $active_range
     */
    public function __construct(Session $session, $active_range)
    {
        $this->session      = $session;
        $this->active_range = $active_range;
    }


    /**
     * Returns the current active range.
     * Note, it can be overwritten if logged into admin
     *
     * @return string
     */
    public function getCurrentRange()
    {
        if ($this->session->has('active_range')) {
            return $this->session->get('active_range');
        }

        return $this->active_range;
    }


    /**
     * Set the current range
     *
     * @param string $name
     */
    public function setCurrentRange($name)
    {
        $name = strtoupper($name);
        $this->validateRange($name);
        $this->active_range = $name;

        $range = SettingsQuery::create()
            ->filterByNs('core')
            ->filterByCKey('active_procuct_range')
            ->findOne()
        ;

        if (!$range instanceof Settings) {
            $range = new Settings();
            $range->setNs('core');
            $range->setCKey('active_procuct_range');
        }

        $range->setCValue($name);
    }


    /**
     * Allows for session overrides of active range
     *
     * @param string $name
     */
    public function setSessionOverride($name)
    {
        $name = strtoupper($name);
        $this->validateRange($name);
        $this->session->set('active_range', $name);
    }


    /**
     * Returns a list of available ranges
     *
     * @return array
     */
    public function getRangeList()
    {
        if (0 === count($this->available_ranges)) {
            $this->loadAvailable();
        }

        return $this->available_ranges;
    }


    /**
     * Loads available ranges from db.
     */
    private function loadAvailable()
    {
        $result = ProductsQuery::create()
            ->select('Range')
            ->distinct()
            ->find()
        ;

        foreach ($result as $range) {
            $range = strtoupper($range);
            $this->available_ranges[$range] = $range;
        }
    }


    /**
     * Validate a range name to see if it's actually available
     * @param string $name
     * @throws \InvalidArgumentException
     */
    private function validateRange($name)
    {
        if (empty($this->available_ranges)) {
            $this->loadAvailable();
        }

        if (!isset($this->available_ranges[$name])) {
            throw new \InvalidArgumentException("Unknown range specified!");
        }
    }
}
