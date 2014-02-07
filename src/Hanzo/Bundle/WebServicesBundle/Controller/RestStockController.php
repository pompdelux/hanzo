<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\ProductsQuery,
    Hanzo\Model\Products,
    Hanzo\Model\ProductsDomainsPricesQuery
;
use Hanzo\Core\CoreController;

/**
 * @see
 *  http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 *  http://speakerdeck.com/u/hhamon/p/silex-meets-soap-rest
 *
 */
class RestStockController extends CoreController
{
    public function indexAction() {}


    /**
     * check stock for a product or collection of products
     * note: only products in stock is returned.
     *
     * @param Request $request
     * @param int     $product_id
     * @param string  $version
     * @return Response json encoded responce
     */
    public function checkAction(Request $request, $product_id = null, $version = 'v1')
    {
        $quantity = $request->get('quantity', 0);
        $translator = $this->get('translator');

        $filters = array();
        if (empty($product_id)) {
            if ($m = $request->get('master')){
                $filters['Master'] = $m;
            }

            if ($s = $request->get('size')){
                $filters['Size'] = $s;
            }

            if ($c = $request->get('color')){
                $filters['Color'] = $c;
            }
        }

        if (empty($product_id) && empty($filters['Master'])) {
            return $this->json_response(array(
                'status'  => FALSE,
                'message' => $translator->trans('Missing parameters.'),
            ), 400);
        }

        if ($product_id) {
            /**
             * the easy one, we have the eccakt product id, fetch it and return the status
             */
            $product = ProductsQuery::create()->findPk($product_id);

            if (!$product instanceof Products) {
                return $this->json_response(array(
                    'status'  => FALSE,
                    'message' => $translator->trans('No such product (#' . $product_id . ')'),
                ), 400);
            }

            $result = $this->get('stock')->check($product);

            $data = array(
                'status' => FALSE,
                'message' => 'Product out of stock',
                'data' => array(
                    'product_id' => $product->getId(),
                    'master'     => $product->getMaster(),
                    'size'       => $product->getSize(),
                    'size_label' => $product->getPostfixedSize($translator),
                    'color'      => $product->getColor(),
                    'date'       => '',
            ));

            if ($result) {
                $data['status'] = TRUE;
                $data['message'] = $translator->trans('Product in stock');
                $data['data']['date'] = ($result instanceof \DateTime ? $result->format('Y m/d') : '');

                return $this->json_response($data, 200);
            }
        } else {
            $query = ProductsQuery::create()
                ->filterByIsOutOfStock(FALSE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
                ->endUse()
                // Be sure to order by size as a number(192) not text(192-198)
                ->withColumn('CONVERT(SUBSTRING_INDEX(products.SIZE,\'-\',1),UNSIGNED INTEGER)','size_num')
                ->orderBy('size_num')
                ->orderBy('color')
                ->groupById()
            ;

            $result = $query->findByArray($filters);

            $data = array();
            $message = $translator->trans('No product(s) in stock.');
            if ($result->count()) {
                $stock = $this->get('stock');
                $stock->prime($result);

                foreach ($result as $record) {
                    if ($dato = $stock->check($record)) {
                        $date = ($dato instanceof \DateTime ? $dato->format('d.m.Y') : '');

                        $data[] = array(
                            'product_id' => $record->getId(),
                            'master'     => $record->getMaster(),
                            'size'       => $record->getSize(),
                            'size_label' => $record->getPostfixedSize($translator),
                            'color'      => $record->getColor(),
                            'date'       => $date
                        );

                        if ($date) {
                            $message = $translator->trans('late.delivery', array('%product%' => $record->getMaster(), '%date%' => $date), 'js');
                        } else {
                            $message = '';
                        }
                    }
                }
                if (count($data)) {
                    $data = array('products' => $data);
                }
            }

            return $this->json_response(array(
                'status'  => TRUE,
                'message' => $message,
                'data'    => $data
            ));
        }


        return $this->json_response(array('status' => FALSE));
    }
}
