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

class CheckStockSyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:ax:stock-sync-checker')
            ->setDescription('Ax stock sync checker')
            ->addArgument('endpoint', InputArgument::REQUIRED, 'What endpoint to check ?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $error = false;
        $endpoint = strtoupper($input->getArgument('endpoint'));

        $output->writeln('Stoc sync checker initiated for: <info>'.$endpoint.'</info>');

        $c = $this->getContainer();
        $last_run = $c->get('redis.permanent')->hget('stock.sync.time', $endpoint);

        if (empty($last_run)) {
            return;
        }

        $now = new DateTime('- 2 hours');
        if ($last_run < $now->getTimestamp()) {
            $error = "Lagersynkroniseringen for {$endpoint} har ikke kørt siden den: ".date('Y-m-d H:i:s', $last_run);
        }

        // NICETO: not hardcoded ...
        if ($error) {
            $header = array(
                "From: pompdelux@pompdelux.dk",
                "Return-Path: pompdelux@pompdelux.dk",
                "Errors-To: pompdelux@pompdelux.dk",
            );

            mail(
                'hd@pompdelux.dk',
                'Fejl fra lagersync checkeren (' . $endpoint . ')', "Fejlbesked:\n" . $error . "\n\n-- \nMr. Miyagi",
                implode("\r\n", $header),
                '-fpompdelux@pompdelux.dk'
            );
        }
    }
}
