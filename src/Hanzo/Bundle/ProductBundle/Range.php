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

/**
 * Class Range
 *
 * @package Hanzo\Bundle\ProductBundle
 */
class Range
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @var string
     */
    private $activeRange;

    /**
     * @var array
     */
    private $availableRanges = [];


    /**
     * Construct
     *
     * @param Session $session
     * @param string  $activeRange
     */
    public function __construct(Session $session, $activeRange)
    {
        $this->session     = $session;
        $this->activeRange = $activeRange;
    }


    /**
     * Returns the current active range.
     * Note, it can be overwritten if logged into admin
     *
     * @return string
     */
    public function getCurrentRange()
    {
        if ($this->session->has('active_product_range')) {
            return $this->session->get('active_product_range');
        }

        return $this->activeRange;
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
        $this->activeRange = $name;

        $range = SettingsQuery::create()
            ->filterByNs('core')
            ->filterByCKey('active_product_range')
            ->findOne();

        if (!$range instanceof Settings) {
            $range = new Settings();
            $range->setNs('core');
            $range->setCKey('active_product_range');
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
        $this->session->set('active_product_range', $name);
    }


    /**
     * Returns a list of available ranges
     *
     * @return array
     */
    public function getRangeList()
    {
        if (0 === count($this->availableRanges)) {
            $this->loadAvailable();
        }

        return $this->availableRanges;
    }


    /**
     * Loads available ranges from db.
     */
    private function loadAvailable()
    {
        $result = ProductsQuery::create()
            ->select('Range')
            ->distinct()
            ->find();

        foreach ($result as $range) {
            $range = strtoupper($range);
            $this->availableRanges[$range] = $range;
        }
    }


    /**
     * Validate a range name to see if it's actually available
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    private function validateRange($name)
    {
        if (empty($this->availableRanges)) {
            $this->loadAvailable();
        }

        if (!isset($this->availableRanges[$name])) {
            throw new \InvalidArgumentException("Unknown range specified!");
        }
    }
}
