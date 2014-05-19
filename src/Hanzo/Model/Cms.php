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
     * Override base fromArray function. This one allows to get any CmsI18ns.
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     *
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        parent::fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME);
        $this->initCmsI18ns();
        if (isset($arr['CmsI18ns'])) {
            foreach ($arr['CmsI18ns'] as $cmsI18n) {
                $translation = new CmsI18n();
                $translation->fromArray($cmsI18n);
                $this->addCmsI18n($translation);
            }
        }
    }
} // Cms
