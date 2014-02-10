<?php /* vim: set sw=4: */

/**
 * usage:
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:ax:stock-trigger dk
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:ax:stock-trigger no
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:ax:stock-trigger se
 */

namespace Hanzo\Bundle\DataIOBundle\Command;

use DateTime;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckJobsCommand extends ContainerAwareCommand
{
    protected $errors = [];

    protected function configure()
    {
        $this->setName('hanzo:dataio:job-checker')
            ->setDescription('Check when jobs has last run.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        #$this->stockSyncCheck($output);
        $this->cronCheck($output);

        // NICETO: not hardcoded ...
        if (count($this->errors)) {
            $header = array(
                "From: pompdelux@pompdelux.dk",
                "Return-Path: pompdelux@pompdelux.dk",
                "Errors-To: pompdelux@pompdelux.dk",
            );

            mail(
                'hd@pompdelux.dk,jm@pompdelux.dk',
                'Fejl i cronjob', "Fejlbesked:\n" . implode("\n", $this->errors) . "\n\n-- \nMr. Miyagi",
                implode("\r\n", $header),
                '-fpompdelux@pompdelux.dk'
            );
        }
    }

    protected function stockSyncCheck($output)
    {
        $output->writeln('Stock sync checker initialized.</info>');
        $runs = $this->getContainer()->get('redis.permanent')->hgetall('stock.sync.time');

        if (empty($runs)) {
            return;
        }

        $now = new DateTime('-2 hours');
        foreach ($runs as $endpoint => $date) {
            if ($date < $now->getTimestamp()) {
                $this->errors[] = "Lagersynkroniseringen for '{$endpoint}' har ikke kørt siden den: ".date('Y-m-d H:i:s', $date);
            }
        }
    }


    protected function cronCheck($output)
    {
        $output->writeln('Dead order cron checker initialized.</info>');
        $runs = $this->getContainer()->get('redis.permanent')->hgetall('cron.log');

        if (empty($runs)) {
            return;
        }

        $now = new DateTime('-6 hours');
        foreach ($runs as $service => $date) {
            if ($date < $now->getTimestamp()) {
                $this->errors[] = "Cron for '{$service}' har ikke kørt siden den: ".date('Y-m-d H:i:s', $date);
            }
        }
    }
}
