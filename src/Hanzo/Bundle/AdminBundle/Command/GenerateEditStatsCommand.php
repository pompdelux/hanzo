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

use Hanzo\Bundle\AdminBundle\Exporter\EditStatsExporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateEditStatsCommand
 *
 * @package Hanzo\Bundle\AdminBundle
 */
class GenerateEditStatsCommand extends ContainerAwareCommand
{
    /**
     * setup the command
     */
    protected function configure()
    {
        $this->setName('hanzo:dataio:generate-edit-stats')
            ->setDescription('Export edit stats for a given period.')
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Start date, Format: YYYY-MM-DD', date('Y-m-d', strtotime('-2 month')))
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'End date, Format: YYYY-MM-DD', date('Y-m-d'))
            ->addOption('db', null, InputOption::VALUE_OPTIONAL, 'Database to dump from.', 'dk')
            ->addOption('target', null, InputOption::VALUE_OPTIONAL, 'Where to store the dump?', './tmp/[db].csv');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exporter = new EditStatsExporter();
        $exporter->setFromDate($input->getOption('from'));
        $exporter->setToDate($input->getOption('to'));

        $conn = \Propel::getConnection('pdldb'.$input->getOption('db').'1');
        $exporter->setDBConnection($conn);

        $target = $input->getOption('target');
        if (false !== strpos($target, '[db]')) {
            $target = './tmp/'.$input->getOption('db').'.csv';
        }

        $dir = realpath(dirname($target));
        if ('' == $dir) {
            throw new \InvalidArgumentException('Target dir "'.dirname($target).'" is not existing or out of bounds.');
        }

        $target = $dir.'/'.basename($target);
        $report = $exporter->getCsvReport();

        file_put_contents($target, $report);

        $output->writeln('<info>Report written to '.$target.'</info>');
    }
}
