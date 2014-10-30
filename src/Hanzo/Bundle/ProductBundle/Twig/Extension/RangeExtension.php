<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\ProductBundle\Twig\Extension;

use Hanzo\Bundle\ProductBundle\Range;

class RangeExtension extends \Twig_Extension
{
    public function __construct(Range $range)
    {
        $this->range = $range;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'range';
    }

    public function getFunctions()
    {
        return [
            'product_range_select' => new \Twig_Function_Method($this, 'productRangeSelect', ['is_safe' => ['html']]),
        ];
    }

    public function productRangeSelect(array $params = [])
    {
        $options = '';
        foreach ($params as $key => $value) {
            $options .= ' '.$key.'="'.$value.'"';
        }

        $output = '<select name="range"'.$options.'>';
        $active = $this->range->getCurrentRange();

        foreach($this->range->getRangeList() as $key => $value) {
            $output .= '<option name="'.$key.'"'.($active === $key ? ' selected' : '').'>'.$value.'</option>';
        }

        $output .= '</select>';

        return $output;
    }
}
