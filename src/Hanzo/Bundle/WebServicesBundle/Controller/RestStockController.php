<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    /**
     * TODO: implement documentation in index actions.
     */
    public function indexAction() {}


    /**
     * check stock for a product or collection of products
     * note: only products in stock is returned.
     *
     * @param int $product_id
     * @param str $version
     * @return Response json encoded responce
     */
    public function checkAction($product_id = null, $version = 'v1')
    {
        $request = $this->get('request');
        $quantity = $request->get('quantity', 0);
        $translator = $this->get('translator');

        $filters = array();
        if (empty($product_id)) {
            if ($request->get('master')){
                $filters['Master'] = $request->get('master');
            }
            if ($request->get('size')){
                $filters['Size'] = $request->get('size');
            }
            if ($request->get('color')){
                $filters['Color'] = $request->get('color');
            }
        }

        if (empty($product_id) && empty($filters['Master'])) {
            return $this->json_response(array(
                'status' => FALSE,
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
                    'status' => FALSE,
                    'message' => $translator->trans('No such product (#' . $product_id . ')'),
                ), 400);
            }

            $result = $this->get('stock')->check($product);

            $data = array(
                'status' => FALSE,
                'message' => 'Product out of stock',
                'data' => array(
                    'product_id' => $product->getId(),
                    'master' => $product->getMaster(),
                    'size' => $product->getSize(),
                    'color' => $product->getColor(),
                    'date' => '',
            ));

            if ($result) {
                $data['status'] = TRUE;
                $data['message'] = $translator->trans('Product in stock');
                $data['data']['date'] = ($result instanceof \DateTime ? $result->format('Y m/d') : '');

                return $this->json_response($data, 200);
            }
        }
        else {
            $query = ProductsQuery::create()
                ->filterByIsOutOfStock(FALSE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
                ->endUse()
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
                        $data[] = array(
                            'product_id' => $record->getId(),
                            'master' => $record->getMaster(),
                            'size' => $record->getSize(),
                            'color' => $record->getColor(),
                            'date' => ($dato instanceof \DateTime ? $dato->format('Y m/d') : '')
                        );
                        $message = $translator->trans('Product(s) in stock');
                    }
                }
                if (count($data)) {
                    $data = array('products' => $data);
                }
            }

            return $this->json_response(array(
                'status' => TRUE,
                'message' => $message,
                'data' => $data
            ));
        }


        return $this->json_response(array('status' => FALSE));
    }
}
