<?php /* vim: set sw=4: */

/**
 * usage:
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:ax:stock-trigger dk
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:ax:stock-trigger no
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:ax:stock-trigger se
 */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use \SoapFault;

class AxTriggerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:ax:stock-trigger')
            ->setDescription('Ax stock sync trigger')
            ->addArgument('endpoint', InputArgument::REQUIRED, 'What endpoint to trigger sync job for ?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $endpoint = $input->getArgument('endpoint');

        $output->writeln('Stoc sync trigger initiated for: <info>'.$endpoint.'</info>');

        $ax = $this->getContainer()->get('ax.out.service.send_stock_sync_trigger');
        $ax->setEndPoint($endpoint);

        $status = $ax->send();

        if (true === $status) {
            $output->writeln('<info>Trigger send, OK.</info>');
            return;
        }

        $error = '';
        if (false === $status) {
            $error = 'AX not available.';
            $output->writeln('Runtime error: <error>'.$error.'</error>');
        } elseif ($status instanceof SoapFault) {
            $error = $status->getMessage();
            $output->writeln('AX communication error: <error>'.$error.'</error>');
        }

        if ($error) {
            $header = array(
                "From: it-drift@pompdelux.dk",
                "Return-Path: it-drift@pompdelux.dk",
                "Errors-To: it-drift@pompdelux.dk",
            );

            mail(
                'it-drift@pompdelux.dk',
                'Fejl fra lagersync cron (' . $endpoint . ')', "Fejlbesked:\n" . $error . "\n\n-- \nMr. Miyagi",
                implode("\r\n", $header),
                '-fit-drift@pompdelux.dk'
            );
        }
    }
}
