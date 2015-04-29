<?php

/**
 * usage:
 *  - run once a week
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:google:sitemap_ping
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:google:sitemap_ping
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:google:sitemap_ping
 *     ...
 */

namespace Hanzo\Bundle\GoogleBundle\Command;

use Hanzo\Core\Hanzo;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapPingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:google:sitemap_ping')
            ->setDescription('Ping google to reread sitemap')
        ;
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
        // When running from cron, we do not know the hostname: http://symfony.com/doc/current/cookbook/console/sending_emails.html#configuring-the-request-context-per-command
        $router = $this->getContainer()->get('router');

        $context = $router->getContext();
        $context->setHost('www.pompdelux.com');
        $context->setScheme('http');

        $route = $router->generate('google_sitemap', ['_locale' => Hanzo::getInstance()->get('core.locale')], TRUE);
        $urlToSubmit = urlencode($route);

        $pingUrl = 'http://www.google.com/webmasters/sitemaps/ping?sitemap='.$urlToSubmit;

        $guzzle = new \Guzzle\Http\Client();
        try
        {
            $guzzle->get($pingUrl)->send();
        }
        catch (\Exception $e)
        {
            $output->writeln($e->getMessage());
        }
    }
}
