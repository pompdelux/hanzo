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
            ->addArgument('element', InputArgument::REQUIRED, 'What should be updated? [translations] [assets_version]')
        ;
    }

    /**
     * executes the job
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @throws Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $element = $input->getArgument('element');
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        switch ($element)
        {
          case 'translations':
              $event = new FilterUpdateEvent( 'translations' );
              $dispatcher->dispatch(Events::updateTranslations, $event);
              break;
          case 'assets_version':
              $event = new FilterUpdateEvent( 'assets_version' );
              $dispatcher->dispatch(Events::incrementAssetsVersion, $event);
              break;
          default:
              throw new Exception( 'Unknown argument' );
              break;
        }
    }
}
