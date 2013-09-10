<?php

namespace Hanzo\Bundle\RetargetingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Hanzo\Model\OrdersPeer;

class DefaultController extends Controller
{
    protected $data = [
        'da_DK' => [
            'cid' => 'P0OKH',
            'tid' => '7908',
        ],
        'de_DE' => [
            'cid' => 'Q7JS3',
            'tid' => '7898',
        ],
        'fi_FI' => [
            'cid' => 'PM46A',
            'tid' => '7906',
        ],
        'nl_NL' => [
            'cid' => 'QSZDW',
            'tid' => '7904',
        ],
        'nb_NO' => [
            'cid' => 'REEZP',
            'tid' => '7902',
        ],
        'sv_SE' => [
            'cid' => '7900',
            'tid' => 'RZULI',
        ],
    ];

    protected $default_locale = 'da_DK';


    /**
     * @Template()
     */
    public function successBlockAction($order_id)
    {
        $order = OrdersPeer::retrieveByPK($order_id);

        return [
            'cid'      => $this->getTrackingId('cid'),
            'tid'      => $this->getTrackingId('tid'),
            'total'    => number_format($order->getTotalPrice(), 2, '.', ''),
            'order_id' => $order->getId(),
            'currency' => $order->getCurrencyCode(),
        ];
    }


    /**
     * @Template()
     */
    public function jsBlockAction()
    {
        return [
            'cid' => $this->getTrackingId('cid')
        ];
    }


    /**
     * Finds the rigth cid or tid to use
     *
     * @param  string $type Either "cid" or "tid"
     * @return string
     */
    protected function getTrackingId($type = 'cid')
    {
        $locale = $this->getRequest()->getLocale();
        if (isset($this->data[$locale][$type])) {
            return $this->data[$locale][$type];
        }

        return $this->data[$this->default_locale][$type];
    }
}
