<?php

namespace Hanzo\Model;

use BasePeer;
use \Hanzo\Core\Hanzo;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\om\BaseCms;

use Hanzo\Model\CmsRevision;
use Hanzo\Model\CmsRevisionQuery;


/**
 * Skeleton subclass for representing a row from the 'cms' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class Cms extends BaseCms
{
    public function getSettings($key = NULL, $raw = true)
    {
        $translator = Hanzo::getInstance()->container->get('translator');
        $test       = $this->getId().'.settings';
        $settings   = trim($translator->trans($test, [], 'cms', $this->getLocale()));

        if ($test == $settings) {
            $settings = parent::getSettings();
        }

        if ($raw) {
            return $settings;
        }

        if (is_scalar($settings) && substr($settings, 0, 1) == '{') {
            $settings = json_decode(stripcslashes($settings));
        }

        if ($key) {
            return (isset($settings->{$key}) ? $settings->{$key} : NULL);
        }

        return $settings;
    }


    /**
     * allow us to override all fields on a cms_i18n object with data in a translation file
     *
     * @param  Translator $translator
     * @return Cms
     */
    public function sourceObject($translator)
    {
        $id = $this->getId();
        foreach (CmsPeer::getFieldNames(BasePeer::TYPE_FIELDNAME) as $key) {
            $k = $id.'.'.$key;

            $trans = $translator->trans($k, [], 'cms');
            if ($trans !== $k) {
                $this->setByName($key, $trans, BasePeer::TYPE_FIELDNAME);
            }
        }

        return $this;
    }

    /**
     * Function to get a certain revision of this page.
     *
     * @param int $timestamp a given timestamp of a revision
     *
     * @return Cms
     *    The revision of this Cms
     */
    public function getRevision($timestamp = null)
    {
        $query = CmsRevisionQuery::create()
            ->filterById($this->getId());
        $revision = null;
        if ($timestamp) {
            $revision = $query->findByCreatedAt($timestamp);
        } else {
            $revision = $query->lastCreatedFirst()->findOne();
        }

        if ($revision instanceof CmsRevision) {
            return $revision->getRevision();
        }

        return null;
    }

    /**
     * Return all revision for this CMS.
     *
     * @return CmsRevisionCollection The revisions
     */
    public function getRevisions()
    {
        $revisions = CmsRevisionQuery::create()
            ->filterById($this->getId())
            ->orderByCreatedAt()
            ->find();

        return $revisions;
    }

    /**
     * Save a CMS as a revision.
     *
     * @param int $timestamp save as a certain revision timestamp. Omit to
     * create a new one.
     */
    public function saveRevision($timestamp = null)
    {
        $revision = null;

        if ($timestamp) {
            $revision = CmsRevisionQuery::create()->fincByCreatedAt($timestamp);
        }

        if (!$revision instanceof CmsRevision) {
            $revision = new CmsRevision();
            $revision->setId($this->getId());
        }

        $revision->setRevision($this);
        $revision->save();
    }
} // Cms
