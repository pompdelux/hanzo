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

            $publishedCmsArray = array();
            foreach ($revisionsToPublish as $revision) {
                if (isset($publishedCmsArray[$revision->getId()])) {
                    // A newer revision of this CMS has already been published in
                    // this cron. Skip this one and delete it.
                    $revisionService->deleteRevision($revision);
                    $output->writeln('Revision deleted for CMS: ' . $revision->getId());

                    continue;
                }

                $cms = CmsQuery::create()
                    ->findOneById($revision->getId(), Propel::getConnection($connection, Propel::CONNECTION_WRITE));

                if ($cms instanceof Cms) {
                    try {
                        $cms = $revisionService->saveCmsFromRevision($cms, $revision);
                    } catch (Exception $e) {
                        $output->writeln($e->getMessage());
                        continue;
                    }

                    // This handles some caching updates.
                    foreach ($cms->getCmsI18ns(null, Propel::getConnection($connection, Propel::CONNECTION_WRITE)) as $translation) {
                        $this->getContainer()->get('event_dispatcher')->dispatch('cms.node.updated', new FilterCMSEvent($cms, $translation->getLocale(), Propel::getConnection($connection, Propel::CONNECTION_WRITE)));
                        // Flush all varnish for this locale. If this is a new
                        // CMS, the menues might get updated.
                        $this->getContainer()->get('varnish.controle')->banUrl('^/' . $translation->getLocale() . '/*');
                    }
                    // Remember which revisions has already been published.
                    $publishedCmsArray[$cms->getId()] = $cms->getId();
                    $output->writeln('Revision published for CMS: ' . $cms->getId());
                } else {
                    $output->writeln('No CMS found with Revision ID: ' . $revision->getId());
                }

            }

            if (count($revisionsToPublish)) {
                // Be sure to clear redis if there have been any new publications.
                $this->getContainer()->get('cache_manager')->clearRedisCache();
            }
        }
        $output->writeln('Cms Revisions published');
    }
}
