<?php
namespace Hanzo\Bundle\CMSBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Bundle\CMSBundle\Controller\DefaultController as CMSController;

/**
 * @todo rename and move
 *
 * @see: http://symfony.com/doc/2.0/cookbook/console.html
 */
class RouterBuilderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hanzo:redis:cache:clear')
            ->setDescription('Clear redis cache')
        ;
    }

    /**
     * @deprecated here until stable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache = $this->getContainer()->get('cache_manager');
        $info = $cache->clearRedisCache();
        $output->writeln('Redis cache cleared: <info>'.$info.'</info>');
    }
}
