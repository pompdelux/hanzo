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

/**
 * CmsRevision Service
 */
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
     * @param Cms             $cms       The Cms Page
     * @param int/CmsRevision $timestamp A given timestamp of a revision, or the
     *                                   actually CmsRevision object
     *
     * @return Cms
     *    The revision of this Cms
     */
    public function getRevision(Cms $cms, $timestamp = null)
    {
        $revision = null;
        if (!$timestamp instanceof CmsRevision) {
            $query = CmsRevisionQuery::create()
                ->filterById($cms->getId());
            if ($timestamp) {
                $revision = $query->findOneByCreatedAt($timestamp, $this->con);
            } else {
                $revision = $query->lastCreatedFirst()->findOne($this->con);
            }
        } else {
            // The given timestamp is actually a CmsRevision.
            $revision = $timestamp;
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
     * @param Cms     $cms                    The Cms Page
     * @param boolean $publishOnDateRevisions Show revisions with a publish
     *                                        date. Default false.
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

        $revisions = $query->find($this->con);

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
                ->findByCreatedAt($timestamp, $this->con);
        }

        if (!$revision instanceof CmsRevision) {
            $revision = new CmsRevision();
            $revision->setId($cms->getId());
        }
        $revision->setPublishOnDate($publishOnDate);
        $revision->setRevision($cms->toArray(BasePeer::TYPE_PHPNAME, true, array(), true));
        $revision->save($this->con);

        // Cleanup, remove the last one.
        if ($this->getRevisionCount($cms) > 10) {
            $lastRevision = CmsRevisionQuery::create()
                ->filterById($cms->getId())
                ->filterByPublishOnDate(null)
                ->orderByCreatedAt()
                ->findOne($this->con);
            $lastRevision->delete($this->con);
        }

        return $revision;
    }

    /**
     * Save a Cms from an revision.
     *
     * @param Cms             $cms      The Cms to save.
     * @param int/CmsRevision $revision The revision to save or timestamp of
     *                                  revision.
     *
     * @return Cms The updated cms node.
     */
    public function saveCmsFromRevision(Cms $cms, $revision)
    {
        $cmsRevision = $this->getRevision($cms, $revision);

        if (!$cmsRevision instanceof Cms) {
            throw new \Exception('No revisions found.');
        }

        $cms->fromArray($cmsRevision->toArray());
        $cms->save($this->con);

        // Create new revision, and delete old one.
        // self::saveRevision($cmsRevision);
        // $revision->delete($this->con);

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
            ->count($this->con);

        return $revisionsCount;
    }

    /**
     * Find all revisions with older pulish date and publish them.
     *
     * @return array the objects of revisions which should be published.
     */
    public function getRevisionsToPublish()
    {
        $revisionsToPublish = CmsRevisionQuery::create()
            ->filterByPublishOnDate(array('max' => time()))
            ->find($this->con);

        return $revisionsToPublish;
    }
}
