<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\BasketBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BasketCleanupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:basket:cleanup')
            ->setDescription('Triggers a flow of cleanup actions based on the baskets state, payment gateway and age.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'If set, the task will not change any orders');
    }

    /**
     * executes the job
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run')
            ? true
            : false;

        $cleaner = $this->getContainer()->get('hanzo.basket.cleanup');
        $cleaner->setDryRun($dryRun);
        $cleaner->setTrigger($this->getName());
        $cleaner->run();
    }
}
