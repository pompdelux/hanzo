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

/**
 * Class AxBeanstalkWorkerCommand
 * @package Hanzo\Bundle\AxBundle
 */
class AxBeanstalkWorkerCommand extends ContainerAwareCommand
{
    /**
     * @var bool
     */
    private $shutdown = false;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('hanzo:ax:pheanstalk-worker')
            ->setDescription('Send orders to ax from beanstalk queue')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Set max number of loops before exit.', 0)
            ->addOption('ttl', null, InputOption::VALUE_OPTIONAL, 'Set ttl on script, will exit script in tts seconds.', 0)
        ;
    }


    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = 0;
        $now  = time();
        $ttl  = (int) $input->getOption('ttl');

        // bind pcntl signals
        $this->bind();

        while ($this->watch()) {
            $loop++;

            if ($ttl && strtotime('now +'.(int)$ttl.' seconds') > $now) {
                exit;
            }

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
     * @return bool
     */
    private function watch()
    {
        if ($this->shutdown) {
            exit;
        }

        /** @var \Leezy\PheanstalkBundle\Proxy\PheanstalkProxy $pheanstalk */
        $pheanstalk = $this->getContainer('leezy.pheanstalk');

        /** @var \Pheanstalk_Job $job */
        $job = $pheanstalk
            ->watch('orders2ax')
            ->ignore('default')
            ->reserve();

        $data = json_decode($job->getData(), true);

        try {
            $this->getContainer()->get('ax.out.pheanstalk.send')->send($data);
        } catch (\Exception $exception) {
            $this->getContainer()->get('logger')->error('AxBeanstalkWorkerCommand: Exception detected: '.$exception->getMessage());
        }

        $pheanstalk->delete($job);

        return true;
    }


    /**
     * Shutdown running process - if in demon mode.
     */
    public function shutdown()
    {
        $this->shutdown = true;
    }


    /**
     * Bind proc signals, so shutdowns will be executed correctly.
     */
    private function bind()
    {
        pcntl_signal(SIGTERM, array($this, 'shutdown'));
        pcntl_signal(SIGQUIT, array($this, 'shutdown'));
        pcntl_signal(SIGINT, array($this, 'shutdown'));
    }
}
