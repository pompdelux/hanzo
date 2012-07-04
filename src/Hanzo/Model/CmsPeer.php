<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseCmsPeer,
    Hanzo\Model\CmsI18nPeer;


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
class CmsPeer extends BaseCmsPeer
{

    public static function getByPK($id, $locale)
    {
        $result = CmsI18nQuery::create()
            ->filterByLocale($locale)
            ->findOneById($id)
        ;

        if ($result instanceof CmsI18n) {
            return $result;
        }

        return NULL;
    }

    public static function getFrontpage($locale)
    {
        $frontpage_id = \Hanzo\Core\Hanzo::getInstance()->container->getParameter('hanzo_cms.frontpage');

        $result = CmsQuery::create()
            ->joinWithI18n($locale)
            // ->filterByType('frontpage')
            ->filterById($frontpage_id)
            ->findOne()
        ;

        if ($result instanceof Cms) {
            return $result;
        }

        return NULL;
    }

} // CmsPeer
