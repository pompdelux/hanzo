<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog;

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

    public function checkAction($id, $version = 'v1')
    {
        $request = $this->get('request');
        $quantity = $request->get('quantity', 0);

        $filters = array();
        if (empty($id)) {
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

        if (empty($id) && empty($filters['Master'])) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Missing parameters.'
            ), 400);
        }

        if ($id) {
            /**
             * the easy one, we have the eccakt product id, fetch it and return the status
             */
            $product = ProductsQuery::create()->findPk($id);

            if (!$product instanceof Products) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'No such product (#' . $id . ')'
                ), 400);
            }

            $result = $this->get('stock')->check($product);
            if ($result) {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => 'Product in stock',
                    'date' => ($result instanceof \DateTime ? $result->format('Y m/d') : '')
                ), 200);
            }
        }
        else {
            $query = ProductsQuery::create()
                ->filterByIsOutOfStock(FALSE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($this->get('hanzo')->get('core.domain_id'))
                ->endUse()
                ->groupById()
            ;

            $result = $query->findByArray($filters);

            $data = array();
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
                    }
                }

                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => '',
                    'data' => $data
                ));
            }
        }


        return $this->json_response(array('status' => FALSE));
    }
}
