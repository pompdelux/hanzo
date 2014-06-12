<?php

namespace Hanzo\Bundle\AccountBundle;

use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesPeer;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class AddressFormatter
{
    protected $locale;
    protected $twig;
    protected $translator;

    /**
     * setup AddressFormatter
     *
     * @param string     $locale     The current locale
     * @param TwigEngine $twig       Templating instance
     * @param Translator $translator Translator instance
     */
    public function __construct($locale, TwigEngine $twig, Translator $translator)
    {
        $this->locale     = $locale;
        $this->twig       = $twig;
        $this->translator = $translator;
    }


    /**
     * Return a formatted address block
     * @param  Addresses $address Addresses object
     * @param  string    $format  Either "html", "txt" or "json"
     * @param  string    $locale  Optional locale, only set if you need to override the current locale
     * @return string
     * @throws \InvalidArgumentException
     */
    public function format(Addresses $address, $format = 'html', $locale = null)
    {
        if (!in_array($format, ['html', 'txt', 'json'])) {
            throw new \InvalidArgumentException('$format: '.$format.' not supported');
        }

        if (is_null($locale)) {
            $locale = $this->locale;
        }

        $address = $address->toArray(\BasePeer::TYPE_FIELDNAME);

        if ('json' == $format) {
            return json_encode($address);
        }

        try {
            $string = $this->twig->render('AccountBundle:AddressFormats:address.'.$locale.'.'.$format.'.twig', $address);
        } catch (\Exception $e) {
            $string = $this->twig->render('AccountBundle:AddressFormats:address.'.$format.'.twig', $address);
        }

        return $string;
    }
}
