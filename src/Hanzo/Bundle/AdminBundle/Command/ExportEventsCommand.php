<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AdminBundle\Command;

use Hanzo\Bundle\AdminBundle\Exporter\EventExporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportEventsCommand
 *
 * @package Hanzo\Bundle\AdminBundle
 */
class ExportEventsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:export-events')
            ->setDescription('Export all events from dd. and the next 24 days.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = $this->getContainer()->get('hanzo.propel_replicator')->getConnectionNames();

        $startDate = date('Y-m-d');
        $endDate   = date('Y-m-d', strtotime('+84 days'));

        $exporter = new EventExporter($startDate, $endDate);
        $dir = $this->getContainer()->get('kernel')->getRootDir().'/../web/images/arrangementer/';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        foreach ($connections as $connection) {
            $exporter->setDBConnection(\Propel::getConnection($connection));
            file_put_contents($dir.'arrangement-'.$connection.'.csv', $exporter->getDataAsCsv());
        }
    }
}
