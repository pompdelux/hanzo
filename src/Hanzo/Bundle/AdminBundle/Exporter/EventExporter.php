<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AdminBundle\Exporter;

use Hanzo\Model\ConsultantsQuery;
use Hanzo\Model\EventsQuery;

/**
 * Class EventExporter
 *
 * @package Hanzo\Bundle\AdminBundle\Exporter
 */
class EventExporter
{
    /**
     * @var string
     */
    private $startDate;

    /**
     * @var string
     */
    private $endDate;

    /**
     * @var \PropelPDO|\PDO
     */
    private $dbConnection;

    /**
     * @param null $startDate
     * @param null $endDate
     */
    public function __construct($startDate = null, $endDate = null)
    {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    /**
     * @param string $date stringtotime() compatible date string
     */
    public function setStartDate($date)
    {
        $this->startDate = $date;
    }

    /**
     * @param string $date stringtotime() compatible date string
     */
    public function setEndDate($date)
    {
        $this->endDate = $date;
    }

    /**
     * @param \PropelPDO|\PDO $connection
     */
    public function setDBConnection($connection)
    {
        $this->dbConnection = $connection;
    }

    /**
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getDataAsCsv()
    {
        if (is_null($this->getDBConnection())) {
            throw new \OutOfBoundsException("Database connection needs to be set.");
        }

        $parser = new \PropelCSVParser();
        $parser->delimiter = ';';

        return $parser->toCSV($this->build(), true, false);
    }


    /**
     * @return \PDO|\PropelPDO
     */
    private function getDBConnection()
    {
        return $this->dbConnection;
    }

    /**
     * @return array
     */
    private function build()
    {
        $dateFilter = [];

        if ($this->startDate && $this->endDate) {
            $dateFilter['min'] = strtotime($this->startDate);
            $dateFilter['max'] = strtotime(date("d-m-Y", strtotime($this->endDate)) . " +1 day");
        } else {
            $dateFilter['min'] = strtotime('-1 month', time());
            $this->startDate    = $dateFilter['min'];
            $dateFilter['max'] = strtotime('Tomorrow');
            $this->endDate      = $dateFilter['max'];
        }

        $data = [];
        $data[0]['consultant'] = 'consultant';

        $consultants = ConsultantsQuery::create()
            ->filterByInitials(null, \Criteria::ISNOTNULL)
            ->filterByInitials('', \Criteria::NOT_EQUAL)
            ->joinCustomers()
            ->useCustomersQuery()
                ->filterByIsActive(true)
                ->filterByGroupsId(2) // Consultants
                ->orderByFirstName()
            ->endUse()
            ->find($this->getDBConnection());

        $events = EventsQuery::create()
            ->filterByEventDate($dateFilter)
            ->orderByHost()
            ->find($this->getDBConnection());

        for ($date = strtotime($this->startDate); $date <= strtotime($this->endDate); $date = strtotime('+1 day', $date)) {
            // Header row with visible dates
            $data[0][date('d-m-Y', $date)] = date('d-m-Y', $date);
        }

        /** @var \Hanzo\Model\Consultants $consultant */
        foreach ($consultants as $consultant) {
            $data[$consultant->getId()][0] = utf8_decode($consultant->getInitials());

            for ($date = strtotime($this->startDate); $date <= strtotime($this->endDate); $date = strtotime('+1 day', $date)) {
                $data[$consultant->getId()][date('d-m-Y', $date)] = '-';
            }
        }

        foreach ($events as $event) {
            if (!isset($data[$event->getConsultantsId()])) {
                continue;
            }

            if ($data[$event->getConsultantsId()][date('d-m-Y', strtotime($event->getEventDate()))] === '-') {
                $data[$event->getConsultantsId()][date('d-m-Y', strtotime($event->getEventDate()))] = $event->getType();
            } else {
                $data[$event->getConsultantsId()][date('d-m-Y', strtotime($event->getEventDate()))] .= "+".$event->getType();
            }
        }

        return $data;
    }
}
