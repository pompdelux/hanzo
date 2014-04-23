<?php

namespace Hanzo\Bundle\CmsBundle\Services;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CmsRevision;
use Hanzo\Model\CmsRevisionQuery;

use \PropelPDO;
use \Propel;
use \BasePeer;

class CmsRevisionService
{
    protected $con;

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->con = Propel::getConnection(CmsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    /**
     * Set Propel Connection.
     * @param PropelPDO $con
     *
     * @return  CmsRevisionService Return this
     */
    public function setCon(PropelPDO $con)
    {
        $this->con = $con;

        return $this;
    }

    /**
     * Function to get a certain revision of this page.
     *
     * @param Cms $cms       The Cms Page
     * @param int $timestamp A given timestamp of a revision
     *
     * @return Cms
     *    The revision of this Cms
     */
    public function getRevision(Cms $cms, $timestamp = null)
    {
        $query = CmsRevisionQuery::create()
            ->filterById($cms->getId());
        $revision = null;
        if ($timestamp) {
            $revision = $query->findOneByCreatedAt($timestamp);
        } else {
            $revision = $query->lastCreatedFirst()->findOne();
        }

        if ($revision instanceof CmsRevision) {
            $cmsRevision = new Cms();
            $cmsRevision->fromArray($revision->getRevision());
            $cmsRevision->setId($cms->getId());
            $cmsRevision->revision = $revision;

            return $cmsRevision;
        }

        return null;
    }

    /**
     * Return all revision for this CMS.
     *
     * @param Cms $cms The Cms Page
     *
     * @return CmsRevisionCollection The revisions
     */
    public function getRevisions(Cms $cms, $publishOnDateRevisions = false)
    {
        $query = CmsRevisionQuery::create()
            ->filterById($cms->getId());

        if ($publishOnDateRevisions) {
            $query->filterByPublishOnDate(null, \Criteria::NOT_EQUAL)
                  ->orderByPublishOnDate('ASC');
        } else {
            $query->filterByPublishOnDate(null)
                  ->orderByCreatedAt('DESC');
        }

        $revisions = $query->find();

        $revisionsArray = array();

        foreach ($revisions as $revision) {
            if ($revision instanceof CmsRevision) {
                $revisionArray = $revision->toArray();
                $revisionArray['Cms'] = $revision->getRevision();
                $revisionsArray[] = $revisionArray;
            }
        }

        return $revisionsArray;
    }

    /**
     * Save a CMS as a revision.
     * @param Cms      $cms           The Cms Page
     * @param int      $timestamp     Save as a certain revision timestamp. Omit to
     *                                create a new one.
     * @param DateTime $publishOnDate The date the revision should be published.
     *
     * @return CmsRevision the saved revision.
     */
    public function saveRevision(Cms $cms, $timestamp = null, $publishOnDate = null)
    {
        $revision = null;

        if ($timestamp) {
            $revision = CmsRevisionQuery::create()
                ->filterById($cms->getId())
                ->findByCreatedAt($timestamp);
        }

        if (!$revision instanceof CmsRevision) {
            $revision = new CmsRevision();
            $revision->setId($cms->getId());
        }
        $revision->setPublishOnDate($publishOnDate);
        $revision->setRevision($cms->toArray(BasePeer::TYPE_PHPNAME, true, array(), true));
        $revision->save();

        // Cleanup, remove the last one.
        if ($this->getRevisionCount($cms) > 10) {
            $lastRevision = CmsRevisionQuery::create()
                ->filterById($cms->getId())
                ->filterByPublishOnDate(null)
                ->orderByCreatedAt()
                ->findOne();
            $lastRevision->delete();
        }

        return $revision;
    }

    /**
     * Save a Cms from an revision.
     *
     * @param Cms $cms                  The Cms to save.
     * @param int/CmsRevision $revision The revision to save or timestamp of
     *                                  revision.
     *
     * @return Cms The updated cms node.
     */
    public function saveCmsFromRevision(Cms $cms, $revision)
    {
        if (!$revision instanceof CmsRevision && is_int($revision)) {
            $revision = CmsRevisionQuery::create()
                ->filterById($cms->getId())
                ->findByCreatedAt($timestamp);
        }

        if (!$revision instanceof CmsRevision) {
            throw new \Exception('No revisions found.');
        }

        $cms->fromArray($revision->getRevision());
        $cms->save();

        // Create new revision, and delete old one.
        self::saveRevision($cms);
        $revision->delete();

        return $cms;
    }

    /**
     * Get the number of revisions a Cms has.
     * @param Cms $cms The Cms Page.
     *
     * @return int     The number of revisions.
     */
    public function getRevisionCount(Cms $cms)
    {
        $revisionsCount = CmsRevisionQuery::create()
            ->filterById($cms->getId())
            ->count();

        return $revisionsCount;
    }

    /**
     * Find all revisions with older pulish date and publish them.
     *
     * @return int the number of revisions which are published.
     */
    public function publishRevisions()
    {
        $query = CmsRevisionQuery::create()
            ->filterByPublishOnDate(array('max' => time()));

        $revisionsToPublish = $query->find();

        $numberOfRevisions = $query->count();

        foreach ($revisionsToPublish as $revision) {
            $cms = CmsQuery::create()
                ->findOneById($revision->getId());

            if ($cms instanceof Cms) {
                $cms = self::saveCmsFromRevision($cms, $revision);

                // This handles some caching updates.
                foreach ($cms->getCmsI18ns() as $translation) {
                    $this->getContainer()->get('event_dispatcher')->dispatch('cms.node.updated', new FilterCMSEvent($cms, $translation->getLocale()));
                }
            }
        }

        if ($numberOfRevisions) {
            // Be sure to clear redis if there have been any new publications.
            $this->getContainer()->get('cache_manager')->clearRedisCache();
        }

        return $numberOfRevisions;
    }
}
