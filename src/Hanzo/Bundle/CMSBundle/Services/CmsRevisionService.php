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
    public function getRevisions(Cms $cms)
    {
        $revisions = CmsRevisionQuery::create()
            ->filterById($cms->getId())
            ->orderByCreatedAt('DESC')
            ->find();

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
     * @param Cms $cms       The Cms Page
     * @param int $timestamp Save as a certain revision timestamp. Omit to
     *                       create a new one.
     */
    public function saveRevision(Cms $cms, $timestamp = null)
    {
        $revision = null;

        if ($timestamp) {
            $revision = CmsRevisionQuery::create()->fincByCreatedAt($timestamp);
        }

        if (!$revision instanceof CmsRevision) {
            $revision = new CmsRevision();
            $revision->setId($cms->getId());
        }
        $revision->setRevision($cms->toArray(BasePeer::TYPE_PHPNAME, true, array(), true));
        $revision->save();
    }
}
