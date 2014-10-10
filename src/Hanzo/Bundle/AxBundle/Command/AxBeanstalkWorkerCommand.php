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
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersToAxQueueLogQuery;
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
    private $isWorking = false;

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
        if (!$input->getOption('quiet')) {
            $output->writeln(sprintf(
                '<comment>[%s]</comment> <info>Loaded. Ctrl+C to break</info>',
                date('Y-m-d H:i:s')
            ));
        }

        $loop = 0;
        $now  = time();
        $ttl  = (int) $input->getOption('ttl');

        // bind pcntl signals
        $this->bind();

        while ($this->watch($input, $output)) {
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
            ->watch('orders2ax')
            ->ignore('default')
            ->reserve();

        $this->isWorking = true;
        $data = json_decode($job->getData(), true);

        if (!$input->getOption('quiet')) {
            $output->writeln(sprintf(
                '<comment>[%s]</comment> <info>Job #'.$job->getId().' received - processing (order id: '.$data['order_id'].', action: '.$data['action'].', db: '.$data['db_conn'].')</info>',
                date('Y-m-d H:i:s')
            ));
        }

        try {
            if (isset($data['action']) && ('delete' === $data['action'])) {
                $this->getContainer()->get('ax.out.pheanstalk.send')->delete($data);
            } else {
                $this->getContainer()->get('ax.out.pheanstalk.send')->send($data);
            }
        } catch (\Exception $exception) {
            $this->halt($data, 'AxBeanstalkWorkerCommand: Exception detected: '.$exception->getMessage());
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


    /**
     * @param array  $data
     * @param string $message
     */
    private function halt(array $data, $message)
    {
        // we need an order record otherwise the db scheme breaks, and we do not want to change that.
        if ('delete' === $data['action']) {
            $ts = strtotime('+1 week');

            $order = new Orders();
            $order->setId($data['order_id']);
            $order->setSessionId($data['order_id']);
            $order->setCustomersId($data['customer_id']);
            $order->setCreatedAt($ts);
            $order->setUpdatedAt($ts);

            try {
                $order->save(\Propel::getConnection($data['db_conn']));
            } catch (\Exception $e) {
                Tools::log($e->getMessage());
            }
        }

        OrdersToAxQueueLogQuery::create()
            ->filterByOrdersId($data['order_id'])
            ->delete(\Propel::getConnection($data['db_conn']));

        $this->getContainer()->get('ax.logger')->write($data['order_id'], 'failed', $data, $message . ' ('.$data['db_conn'].')');
    }
}
