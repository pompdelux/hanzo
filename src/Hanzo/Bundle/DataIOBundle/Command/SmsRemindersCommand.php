<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Propel;
use PDO;

use \Exception;
use \PropelCollection;

class SmsRemindersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:sms:reminders')
            ->setDescription('Sends SMS reminders')
            ->addArgument('locale', InputArgument::REQUIRED, 'What language (locale) to trigger reminders for ?')
        ;
    }


    /**
     * executes the job
     *
     * @NICETO: catch errors
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $input->getArgument('locale');

        $output->writeln('SMS reminders send for locale: <info>'.$locale.'</info>');
        $sms = $this->getContainer()->get('sms_manager');

        $status = $sms->eventReminder($locale);
        $output->writeln('<info>Reminders send.</info>');

        $prefix = substr($this->getContainer()->getParameter('locale'), -2);
        $this->getContainer()->get('pdl.phpredis.permanent')->hset('cron.log', $prefix.':sms_reminders', time());
    }
}
