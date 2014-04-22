<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\GoogleBundle\Services\AddWords;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class AddWordsConversion
{
    /**
     * @var ParameterBag
     */
    private $settings;

    /**
     * @param int    $id
     * @param string $language
     * @param string $format
     * @param string $color
     * @param string $label
     * @param string $value
     * @param bool   $remarketing_only
     */
    public function __construct($id, $language = 'en', $format = null, $color = 'ffffff', $label = null, $value = null, $remarketing_only = false)
    {
        $this->settings = new ParameterBag([
            'id'               => $id,
            'language'         => $language,
            'format'           => $format,
            'color'            => $color,
            'label'            => $label,
            'value'            => $value,
            'remarketing_only' => ($remarketing_only ? 'true' : 'false'),
        ]);
    }

    /**
     * Overwrite a setting, note only 'label', 'value', 'color', 'format' fields can be overwritten.
     *
     * @param string $key
     * @param mixed  $value
     * @return bool
     * @throws InvalidArgumentException
     */
    public function setParameter($key, $value)
    {
        if (in_array($key, ['label', 'value', 'color', 'format'])) {
            $this->settings->set($key, $value);

            return true;
        }

        throw new InvalidArgumentException("'{$key}' is not an allowed argument to set.");
    }

    /**
     * Return the html for google conversions.
     * @return string
     */
    public function getHtml()
    {
        if ('' === $this->settings->get('id')) {
            return '';
        }

        return trim('
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = '.$this->settings->get('id').';
var google_conversion_language = "'.$this->settings->get('language').'";
var google_conversion_format = "'.$this->settings->get('format').'";
var google_conversion_color = "'.$this->settings->get('color').'";
var google_conversion_label = "'.$this->settings->get('label').'";
var google_conversion_value = '.$this->settings->get('value').';
var google_remarketing_only = '.$this->settings->get('remarketing_only').';
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/'.$this->settings->get('id').'/?value='.$this->settings->get('value').'&amp;label='.$this->settings->get('label').'&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
');

    }
}
