<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class MovePdfDocuments
 *
 * @package Hanzo\Bundle\AxBundle\Command
 */
class MovePdfDocumentsCommand extends ContainerAwareCommand
{
    /**
     * @var array
     */
    private $failedFiles = [];

    /**
     * @var bool
     */
    private $dryRun = false;

    /**
     * @var string
     */
    private $failedDir;

    /**
     * setup command options
     */
    protected function configure()
    {
        $this->setName('hanzo:ax:move-pdf-files')
            ->setDescription('Move PDF files from ax to domain/year folders.')
            ->addOption('dry-run', null, InputOption::VALUE_OPTIONAL, 'If set only show which files to move to where.', false);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('_dk' != substr($input->getOption('env'), -3)) {
            throw new \InvalidArgumentException("This script must be run in the main _dk env scope!");
        }

        if ($input->getOption('dry-run')) {
            $this->dryRun = true;
        }

        $connections     = $this->getDBConnections();
        $inputDir        = $this->getContainer()->getParameter('pdfupload_root_dir');
        $targetDir       = $inputDir . '/%s/%d';
        $this->failedDir = $inputDir . '/failed';

        $files = Finder::create()
            ->files()
            ->name('*.pdf')
            ->in($inputDir)
            ->depth('== 0')
            ->followLinks();

        $yearSql = "SELECT YEAR(created_at) AS year FROM orders WHERE id = :orderId";

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $basename = $file->getBasename('.pdf');

            if (!preg_match('/^[A-Z]{2}_[0-9a-zA-Z]+_[0-9]+$/', $basename)) {
                $this->failedFiles[] = [
                    'type' => 'invalid file name format',
                    'file' => $file,
                ];
                continue;
            }

            list($country, $number, $orderId) = explode('_', $basename);
            if (empty($orderId)) {
                $this->failedFiles[] = [
                    'type' => 'invalid file name format',
                    'file' => $file,
                ];
                continue;
            }

            $connection = 'pdldb'.strtolower($country).'1';

            // default connections to dk1.
            if (!isset($connections[$connection])) {
                $connection = 'pdldbdk1';
            }

            $conn = \Propel::getConnection($connection);
            $stmt = $conn->prepare($yearSql);
            $stmt->execute(['orderId' => $orderId]);

            $year = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (empty($year)) {
                $this->failedFiles[] = [
                    'type' => 'order not found',
                    'file' => $file,
                ];

                continue;
            }

            $newTartet = sprintf($targetDir, $country, $year['year']);

            if ($this->dryRun) {
                $output->writeln('<info>Would move <comment>"'.$file->getRealPath().'"</comment> to <comment>"'.$newTartet.'/'.$file->getBasename().'"</comment></info>');
            } else {
                if (!is_dir($newTartet)) {
                    mkdir($newTartet, 0755, true);
                }
                rename($file->getRealPath(), $newTartet.'/'.$file->getBasename());
            }
        }

        $this->handleFailedFiles($output);
    }

    /**
     * get all available db connections
     *
     * @return array
     */
    private function getDBConnections()
    {
        $connections = [];
        foreach ($this->getContainer()->get('hanzo.propel_replicator')->getConnectionNames() as $connection) {
            if ('default' === $connection) {
                continue;
            }

            $connections[$connection] = $connection;
        }

        return $connections;
    }

    /**
     * handle failed files
     *
     * @param OutputInterface $output
     */
    private function handleFailedFiles(OutputInterface $output)
    {
        foreach ($this->failedFiles as $data) {
            $output->writeln('<info>File: <comment>"'.$data['file']->getBasename().'"</comment> failed, error was: "'.$data['type'].'" - file moved to "trash"</info>');

            if (!$this->dryRun) {
                rename($data['file']->getPath(), $this->failedDir.'/'.$data['file']->getBasename());
            }
        }
    }
}
