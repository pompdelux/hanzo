<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseCmsPeer;


/**
 * Skeleton subclass for performing query and update operations on the 'cms' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class CmsPeer extends BaseCmsPeer {

  public static function getByPK($id, $locale)
  {

    $result = CmsQuery::create()
      ->joinWithI18n($locale, \Criteria::INNER_JOIN)
      ->useI18nQuery($locale)
        ->filterById($id)
      ->endUse()
      ->findOne()
    ;

    if ($result instanceof Cms) {
      $settings = $result->getSettings();
      if (substr($settings, 0, 2) == 'a:') {
        $result->setSettings(unserialize($settings));
      }

      return $result;
    }

    return NULL;
  }

} // CmsPeer
