<?php

namespace Hanzo\Twig\Extension;

use Hanzo\Bundle\ServiceBundle\Services\TwigStringService;

use Twig_Extension;
use Twig_Function_Method;
use Twig_Function_Function;
use Twig_Filter_Method;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

class MiscExtension extends Twig_Extension
{
    protected $twig_string;

    public function __construct(TwigStringService $twig_string)
    {
        $this->twig_string = $twig_string;
    }

    public function getName()
    {
        return 'misc';
    }


    public function getFunctions()
    {
        return array(
            'print_r' => new Twig_Function_Function('print_r'),
            'parse' => new Twig_Function_Method($this, 'parse', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    public function getFilters() {
        return array(
            'money' => new Twig_Filter_Method($this, 'hanzo_money_format'),
        );
    }

    /**
     * @see Hanzo\Core\Tools\Tools::moneyFormat
     * TODO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function hanzo_money_format($number, $format = '%i')
    {
        return Tools::moneyFormat($number, $format);
    }


    /**
     * Returns a "template string" parsed by Twig_String
     *
     * @param string $string
     * @param array $parameters
     * @return string
     */
    public function parse($string, $parameters = array())
    {
        return $this->twig_string->parse($string, $parameters);
    }
}
