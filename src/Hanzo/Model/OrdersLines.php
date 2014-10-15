<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseOrdersLines;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class OrdersLines
 *
 * @package Hanzo\Model
 */
class OrdersLines extends BaseOrdersLines
{
    /**
     * Adds postfix to the size label.
     *
     * @param Translator $translator
     *
     * @see Hanzo\Model\Products::getPostfixedSize
     * @return string
     */
    public function getPostfixedSize(Translator $translator)
    {
        $size = $this->getProductsSize();

        // if there are any text in the size value, we do not postfix "One size cm" is just wiered ;)
        if (preg_match('/[a-z]/i', $size)) {
            return $size;
        }

        $sizeLabel = $translator->trans('size.label.postfix');
        if ('size.label.postfix' === $sizeLabel) {
            $sizeLabel = '';
        }

        return $size.$sizeLabel;
    }


    /**
     * @param string $v
     *
     * @return OrdersLines
     */
    public function setPrice($v)
    {
        return parent::setPrice(number_format($v, 2, '.', ''));
    }


    /**
     * @param string $v
     *
     * @return OrdersLines
     */
    public function setOriginalPrice($v)
    {
        return parent::setOriginalPrice(number_format($v, 2, '.', ''));
    }
}
