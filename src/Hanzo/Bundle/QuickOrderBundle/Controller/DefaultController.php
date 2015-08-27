<?php

namespace Hanzo\Bundle\QuickOrderBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ProductsQuery;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\QuickOrderBundle
 */
class DefaultController extends CoreController
{

    /**
     * All logic is a copy of BasketBundle.Default.viewAction
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $order        = OrdersPeer::getCurrent();
        $translator   = $this->container->get('translator');
        $router       = $this->get('router');
        $products     = [];
        $deliveryDate = 0;
        $productRange = $this->container->get('hanzo_product.range')->getCurrentRange();

        // product lines- if any
        foreach ($order->getOrdersLiness() as $line) {
            $line->setProductsSize($line->getPostfixedSize($translator));
            $line = $line->toArray(\BasePeer::TYPE_FIELDNAME);

            if ($line['type'] != 'product') {
                continue;
            }

            $line['expected_at'] = new \DateTime($line['expected_at']);

            $t = $line['expected_at']->getTimestamp();
            if (($t > 0) && ($t > time())) {
                $line['expected_at'] = $t;
                if ($deliveryDate < $line['expected_at']) {
                    $deliveryDate = $line['expected_at'];
                }
            } else {
                $line['expected_at'] = null;
            }

            // we need the id and sku from the master record to generate image and url to product.
            $sql = "
                SELECT p.id, p.sku FROM products AS p
                WHERE p.sku = (
                    SELECT pp.master FROM products AS pp
                    WHERE  pp.id = ".$line['products_id']."
                    AND    pp.range = '".$productRange."'
                )
                AND p.range = '".$productRange."'
            ";
            $master = \Propel::getConnection()
                ->query($sql)
                ->fetch(\PDO::FETCH_OBJ);

            $line['basket_image'] =
                preg_replace('/[^a-z0-9]/i', '-', $master->sku) .
                '_' .
                preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $line['products_color'])) .
                '_overview_01.jpg'
            ;

            $line['url'] = '#';
            if ($master) {
                $line['url']    = $router->generate('product_info', ['product_id' => $master->id]);
                $line['master'] = $master->sku;
            }

            $products[] = $line;
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        return $this->render('QuickOrderBundle:Default:index.html.twig', [
            'embedded'        => false,
            'page_type'       => 'basket',
            'products'        => $products,
            'total'           => $order->getTotalPrice(true),
            'delivery_date'   => $deliveryDate,
        ]);
    }

    /**
     * Fetch sku based on product title.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getSkuAction(Request $request)
    {
        $name    = $request->query->get('name');

        $products = ProductsQuery::create()
            ->where('products.MASTER IS NULL')
            ->filterByIsOutOfStock(false)
            ->filterByRange($this->container->get('hanzo_product.range')->getCurrentRange())
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
            ->endUse()
            ->useProductsI18nQuery()
                ->filterByTitle($name.'%')
                ->filterByLocale($request->getLocale())
            ->endUse()
            ->groupBySku()
            ->orderBySku()
            ->limit($maxRows)
            ->find();

        $result = [];
        if ($products->count()) {
            foreach ($products as $product) {
                $product->setLocale($request->getLocale());

                $result[] = [
                    'name'   => $product->getSku(),
                    'tokens' => [$product->getTitle()],
                    'value'  => $product->getTitle(),
                ];
            }

            if (count($result)) {
                return $this->json_response($result);
            }
        }

        return $this->json_response([$this->get('translator')->trans('quickorder.no.products.found', [], 'quickorder')]);
    }
}
