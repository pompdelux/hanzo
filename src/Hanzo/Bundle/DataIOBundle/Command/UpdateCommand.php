<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Bundle\DataIOBundle\Events,
    Hanzo\Bundle\DataIOBundle\FilterUpdateEvent;

use Exception;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:update')
            ->setDescription('Updates the system')
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
        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $event = new FilterUpdateEvent( 'assets_version' );
        $dispatcher->dispatch(Events::incrementAssetsVersion, $event);
    }
}
