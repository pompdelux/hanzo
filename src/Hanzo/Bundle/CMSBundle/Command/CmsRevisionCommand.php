<?php
namespace Hanzo\Bundle\CMSBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsQuery;
use Hanzo\Bundle\AdminBundle\Event\FilterCMSEvent;

use Hanzo\Core\PropelReplicator;
use \Propel;

class CmsRevisionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hanzo:cms:publish_revisions')
            ->setDescription('Publishes any revisions set with a publish date.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = $this->getContainer()->get('hanzo.propel_replicator')->getConnectionNames();
        foreach ($connections as $connection) {
            $output->writeln('Publishing on database: ' . $connection);
            $revisionService = $this->getContainer()->get('cms_revision')->setCon(Propel::getConnection($connection, Propel::CONNECTION_WRITE));
            $revisionsToPublish = $revisionService->getRevisionsToPublish();

            foreach ($revisionsToPublish as $revision) {
                $cms = CmsQuery::create()
                    ->findOneById($revision->getId());
\Hanzo\Core\Tools::log('id: ' . $revision->getPublishOnDate());
                if ($cms instanceof Cms) {
                    $cms = $revisionService->saveCmsFromRevision($cms, $revision);

                    // This handles some caching updates.
                    foreach ($cms->getCmsI18ns() as $translation) {
                        $this->getContainer()->get('event_dispatcher')->dispatch('cms.node.updated', new FilterCMSEvent($cms, $translation->getLocale()));
                    }
                }
            }
\Hanzo\Core\Tools::log(count($revisionsToPublish));
            if (count($revisionsToPublish)) {
                // Be sure to clear redis if there have been any new publications.
                $this->getContainer()->get('cache_manager')->clearRedisCache();
            }
        }
        $output->writeln('Cms Revisions published');
    }
}
