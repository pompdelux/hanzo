<?php

namespace Hanzo\Model;

use Hanzo\Core\Hanzo;
use Hanzo\Model\om\BaseCmsI18n;

/**
 * Skeleton subclass for representing a row from the 'cms_i18n' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Hanzo\Model
 */
class CmsI18n extends BaseCmsI18n
{
    /**
     * @param bool $raw
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function getSettings($raw = true)
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

        return $settings;
    }
} // CmsI18n
