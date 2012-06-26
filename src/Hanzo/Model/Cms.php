<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseCms;


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
        $settings = parent::getSettings();

        if ($raw) {
            return $settings;
        }

        if (is_scalar($settings) && substr($settings, 0, 2) == '{"') {
            $settings = json_decode(stripcslashes($settings));
        }

        if ($key) {
            return (isset($settings->{$key}) ? $settings->{$key} : NULL);
        }

        return $settings;
    }
} // Cms
