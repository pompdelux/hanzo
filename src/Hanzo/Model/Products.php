<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseProducts;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;


/**
 * Skeleton subclass for representing a row from the 'products' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class Products extends BaseProducts
{
    /**
     * Adds postfix to the size label.
     *
     * @see Hanzo\Model\OrdersLines::getPostfixedSize
     * @param Translator $translator
     * @return string
     */
    public function getPostfixedSize(Translator $translator)
    {
        $size = $this->getSize();

        // if there are any text in the size value, we do not postfix "One size cm" is just wiered ;)
        if (preg_match('/[a-z]/i', $size)) {
            return $size;
        }

        return $size.$translator->trans('size.label.postfix');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMaster().' '.$this->getSize().' '.$this->getColor();
    }

} // Products
