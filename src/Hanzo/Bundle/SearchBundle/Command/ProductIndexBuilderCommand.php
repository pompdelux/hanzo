<?php
namespace Hanzo\Bundle\SearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
* @author Henrik Farre <hf@bellcom.dk>
*/
class ProductIndexBuilderCommand extends ContainerAwareCommand
{
    /**
     * @var bool
     */
    private $shutdown = false;
    private $isWorking = false;

    private $builder = null;

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
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('hanzo:search:phenstalk-indexbuilder-worker')
            ->setDescription('Reindexes search_products_tags if command is in beanstalk queue')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Set max number of loops before exit.', 0)
            ->addOption('ttl', null, InputOption::VALUE_OPTIONAL, 'Set ttl on script, will exit script in tts seconds.', 0)
            ->addOption('once', null, InputOption::VALUE_OPTIONAL, 'Only run once, do not listen for jobs in beanstalk', false);
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
        $this->builder = $this->getContainer()->get('hanzo_search.product.index_builder');

        if (!$input->getOption('quiet')) {
            $output->writeln(sprintf(
                '<comment>[%s]</comment> <info>Loaded. Ctrl+C to break</info>',
                date('Y-m-d H:i:s')
            ));
        }

        $loop = 0;
        $now  = time();
        $ttl  = (int) $input->getOption('ttl');
        $once = $input->getOption('once');

        if ($once === false) {
            // Watch beanstalk
            // bind pcntl signals
            $this->bind();

            while ($this->watch($input, $output)) {
                $loop++;

                if ($ttl && strtotime('now +'.(int) $ttl.' seconds') > $now) {
                    exit;
                }

                if ($input->getOption('limit') && $loop > $input->getOption('limit')) {
                    exit;
                }

                // handle term signals
                pcntl_signal_dispatch();
            }
        } else {
            $this->builder->build();

            return;
        }
    }

    /**
     * @param string $jobName
     */
    protected function notifyAboutCompletedJob($jobName)
    {
        $txt = '';
        mail(
            'hd@pompdelux.dk,cc@pompdelux.dk,pdl@bellcom.dk',
            'Indekserings job "'.$jobName.'" færdigt',
            $txt,
            "Reply-To: hd@pompdelux.dk\r\nReturn-Path: pompdelux@pompdelux.com\r\nErrors-To: pompdelux@pompdelux.com\r\n",
            '-fpompdelux@pompdelux.com'
        );
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
            ->watch('search-index')
            ->ignore('default')
            ->reserve();

        $this->isWorking = true;
        $data = json_decode($job->getData(), true);

        if (!$input->getOption('quiet')) {
            $output->writeln(sprintf(
                '<comment>[%s]</comment> <info>Job #'.$job->getId().' received - reindexing search index</info>',
                date('Y-m-d H:i:s')
            ));
        }

        try {
            if (isset($data['action'])) {
                switch ($data['action'])
                {
                    case 'clear':
                        $this->builder->clear();
                        $this->notifyAboutCompletedJob($data['action']);
                        break;

                    case 'update':
                        $indexesToUpdate = isset($data['indexes_to_update']) ? [] : $data['indexes_to_update'];
                        $this->builder->build($indexesToUpdate);
                        $this->notifyAboutCompletedJob($data['action']);
                        break;
                }
            }
        } catch (\Exception $exception) {
        }

        $pheanstalk->delete($job);
        $this->isWorking = false;

        return true;
    }

    /**
     * Bind proc signals, so shutdowns will be executed correctly.
     */
    private function bind()
    {
        pcntl_signal(SIGALRM, [$this, 'shutdown']);
        pcntl_signal(SIGINT, [$this, 'shutdown']);
        pcntl_signal(SIGQUIT, [$this, 'shutdown']);
        pcntl_signal(SIGTERM, [$this, 'shutdown']);
        pcntl_alarm(2);
    }
}
