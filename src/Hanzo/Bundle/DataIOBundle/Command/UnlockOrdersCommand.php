<?php /* vim: set sw=4: */

/**
 * usage:
 *  - run every 3 hours
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:dataio:unlock_orders
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:dataio:unlock_orders
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:dataio:unlock_orders
 */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Criteria;
use Propel;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;

class UnlockOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:unlock_orders')
            ->setDescription('Unlocks orders in a stale edit mode')
        ;
    }

    /**
     * executes the job
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $container->get('cleanup_manager')->cancelStaleOrderEdit($container);
    }
}
