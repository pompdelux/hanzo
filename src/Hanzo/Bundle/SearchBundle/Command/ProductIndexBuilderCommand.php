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
    private $quiet = false;

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
     *
     * Examples on how to run:
     * - Delete all type:discount and reindex them
     * php app/console hanzo:search:phenstalk-indexbuilder-worker --quiet --once=yes --actions=clear,build --indexes=discount
     *
     * - Truncate and reindex all
     * php app/console hanzo:search:phenstalk-indexbuilder-worker --quiet --once=yes --actions=truncate,build --indexes=ALL
     *
     */
    protected function configure()
    {
        $this->setName('hanzo:search:phenstalk-indexbuilder-worker')
            ->setDescription('Reindexes search_products_tags if command is in beanstalk queue')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Set max number of loops before exit.', 0)
            ->addOption('ttl', null, InputOption::VALUE_OPTIONAL, 'Set ttl on script, will exit script in tts seconds.', 0)
            ->addOption('once', null, InputOption::VALUE_OPTIONAL, 'Only run once, do not listen for jobs in beanstalk', false)
            ->addOption('actions', null, InputOption::VALUE_OPTIONAL, '(Use with --once) Which Actions to run, possible values: clear, truncate, build. Seperate multiple actions with ","', false)
            ->addOption('indexes', null, InputOption::VALUE_OPTIONAL, '(Use with --once) Which indexes to update. Seperate multiple actions with "," or use "ALL"', false);
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

        $this->quiet = $input->getOption('quiet');

        if (!$this->quiet) {
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
            $action = $input->getOption('actions');

            $indexes = $input->getOption('indexes');
            if (strpos($indexes, ',') !== false) {
                $indexes = explode(',', $indexes);
            } else {
                $indexes = [$indexes];
            }

            if (strpos($action, ',') !== false) {
                $actions = explode(',', $action);
                foreach ($actions as $action) {
                    $this->performAction($action, $indexes);
                }
            } else {
                $this->performAction($action, $indexes);
            }

            return;
        }
    }

    /**
     * @param string $jobName
     */
    protected function notifyAboutCompletedJob($jobName)
    {
        if (!$this->quiet) {
            $txt = '';
            mail(
                'it-drift@pompdelux.dk,pdl@bellcom.dk',
                'Indekserings job "'.$jobName.'" færdigt',
                $txt,
                "Reply-To: it-drift@pompdelux.dk\r\nReturn-Path: it-drift@pompdelux.dk\r\nErrors-To: it-drift@pompdelux.dk\r\n",
                '-fit-drift@pompdelux.dk'
            );
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

        if (!$this->quiet) {
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

        if (!$this->quiet) {
            $output->writeln(sprintf(
                '<comment>[%s]</comment> <info>Job #'.$job->getId().' received - reindexing search index</info>',
                date('Y-m-d H:i:s')
            ));
        }

        try {
            if (isset($data['action']) && isset($data['indexes'])) {
                $this->performAction($data['action'], $data['indexes']);
            }
        } catch (\Exception $exception) {
        }

        $pheanstalk->delete($job);
        $this->isWorking = false;

        return true;
    }

    /**
     * @param mixed $action
     * @param mixed $data
     */
    private function performAction($action, Array $indexes = [])
    {
        $this->builder->setIndexes($indexes);

        switch ($action)
        {
            case 'clear':
                $this->builder->clear();
                break;

            case 'truncate':
                $this->builder->truncate();
                break;

            case 'build':
                $this->builder->build();
                break;
        }

        $this->notifyAboutCompletedJob($action);
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
