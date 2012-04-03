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
 * @see: http://symfony.com/doc/2.0/cookbook/console.html
 */
class RouterBuilderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hanzo:router:builder')
            ->setDescription('Generate the router files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache = $this->getContainer()->get('cache_manager');
        $result = $cache->routerBuilder();

        if (is_array($result)) {
            list($routers, $categories) = $result;
            $output->writeln('Routers saved to: <info>'.$routers.'</info>');
            $output->writeln('Category map saved to: <info>'.$categories.'</info>');
        }

        $info = $cache->clearRedisCache();
        $output->writeln('Redis cache cleared: <info>'.$info.'</info>');

        // // clear cache after updating routers
        $command = $this->getApplication()->find('cache:clear');
        $arguments = array(
            'command' => 'cache:clear',
            '--env' => 'prod'
        );

        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
    }
}
