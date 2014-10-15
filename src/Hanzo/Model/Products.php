<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseProducts;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class Products
 *
 * @package Hanzo\Model
 */
class Products extends BaseProducts
{
    /**
     * Adds postfix to the size label.
     *
     * @param Translator $translator
     *
     * @see Hanzo\Model\OrdersLines::getPostfixedSize
     * @return string
     */
    public function getPostfixedSize(Translator $translator)
    {
        $size = $this->getSize();

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
     * @return string
     */
    public function __toString()
    {
        return $this->getMaster().' '.$this->getSize().' '.$this->getColor();
    }

} // Products
