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
        $disallowed_product_categories_postfix = array(
          'AmbroseSocksSS16',
          'YpresSocksSS16',
          'BoiseKNEESOCKSAW15',
          'AmarilloSOCKSAW15',
          'TalbottSOCKSAW15'
        );
        $size = $this->getSize();

        // if there are any text in the size value, we do not postfix "One size cm" is just wiered ;)
        if (preg_match('/[a-z]/i', $size)) {
            return $size;
        }

        // If we shouldnt show postfix for the product category
        if (in_array($this->master, $disallowed_product_categories_postfix)) {
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
