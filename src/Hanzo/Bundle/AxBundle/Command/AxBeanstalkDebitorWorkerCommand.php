<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(ticks=1);

namespace Hanzo\Bundle\AxBundle\Command;

use Hanzo\Core\Tools;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AxBeanstalkDebitorWorkerCommand
 *
 * @package Hanzo\Bundle\AxBundle
 */
class AxBeanstalkDebitorWorkerCommand extends ContainerAwareCommand
{
    /**
     * @var bool
     */
    private $shutdown = false;
    private $isWorking = false;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('hanzo:ax:pheanstalk-debitor-worker')
            ->setDescription('Send debitors to ax from beanstalk queue')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Set max number of loops before exit.', 0);
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Disable instance pooling when operating in a loop..
        \Propel::disableInstancePooling();

        if (!$input->getOption('quiet')) {
            $output->writeln(sprintf(
                '<comment>[%s]</comment> <info>Loaded. Ctrl+C to break</info>',
                date('Y-m-d H:i:s')
            ));
        }

        $loop = 0;

        // bind pcntl signals
        $this->bind();

        while ($this->watch($input, $output)) {
            $loop++;

            if ($input->getOption('limit') && $loop > $input->getOption('limit')) {
                exit;
            }

            // handle term signals
            pcntl_signal_dispatch();
        }
    }


    /**
     * Watch for incoming jobs
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    private function watch(InputInterface $input, OutputInterface $output)
    {
        if ($this->shutdown) {
            exit;
        }

        if (!$input->getOption('quiet')) {
            $output->writeln(sprintf(
                '<comment>[%s]</comment> <info>Watching for incomming jobs ...</info>',
                date('Y-m-d H:i:s')
            ));
        }

        /** @var \Leezy\PheanstalkBundle\Proxy\PheanstalkProxy $pheanstalk */
        $pheanstalk = $this->getContainer()->get('leezy.pheanstalk');

        /** @var \Pheanstalk_Job $job */
        $job = $pheanstalk
            ->watch('debitor2ax')
            ->ignore('default')
            ->reserve();

        $this->isWorking = true;
        $data = json_decode($job->getData(), true);

        if (!$input->getOption('quiet')) {
            $output->writeln(sprintf(
                '<comment>[%s]</comment> <info>Job #'.$job->getId().' received - processing (debitor id: '.$data['customer_id'].', db: '.$data['db_conn'].')</info>',
                date('Y-m-d H:i:s')
            ));
        }

        try {
            $this->getContainer()->get('ax.out.pheanstalk.send_debitor')->send($data);
        } catch (\Exception $exception) {
            Tools::log('AxBeanstalkDebitorWorkerCommand: Exception detected: '.$exception->getMessage());
        }

        $pheanstalk->delete($job);
        $this->isWorking = false;

        return true;
    }


    /**
     * Shutdown running process - if in demon mode.
     *
     * @param int $signal pcntl signal
     */
    public function shutdown($signal)
    {
        $map = [
            SIGINT  => 'SIGINT',
            SIGQUIT => 'SIGQUIT',
            SIGTERM => 'SIGTERM',
            SIGHUP  => 'SIGHUP',
        ];

        if (isset($map[$signal])) {
            if (!$this->isWorking) {
                exit;
            }

            $this->shutdown = true;
        }
    }


    /**
     * Bind proc signals, so shutdowns will be executed correctly.
     */
    private function bind()
    {
        pcntl_signal(SIGALRM, [$this, 'shutdown']);
        pcntl_signal(SIGINT,  [$this, 'shutdown']);
        pcntl_signal(SIGQUIT, [$this, 'shutdown']);
        pcntl_signal(SIGTERM, [$this, 'shutdown']);
        pcntl_alarm(2);
    }
}
