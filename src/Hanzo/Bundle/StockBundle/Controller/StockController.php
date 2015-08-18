<?php

namespace Hanzo\Bundle\StockBundle\Controller;

use Hanzo\Core\Tools;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Monolog;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\Products;

class StockController extends CoreController
{
    /**
     * check stock for a product or collection of products
     * note: only products in stock is returned.
     *
     * @Route("/stock-check/{product_id}", requirements={"product_id" = "\d+"}, defaults={"product_id"=null})
     * @Method({"GET"})
     *
     * @param  Request $request
     * @param  int     $product_id
     * @return Response json encoded responce
     */
    public function checkAction(Request $request, $product_id = null)
    {
        $translator = $this->get('translator');
        $filters    = $this->requestToFilters($product_id, $request);

        if (empty($product_id) && empty($filters['Master'])) {
            return $this->json_response([
                'message' => $translator->trans('Missing parameters.'),
                'status'  => false,
            ], 400);
        }

        if ($product_id) {
            return $this->exactProduct($product_id);
        }

        return $this->filteredProduct($filters);
    }


    /**
     * Turn a request into a filter
     *
     * @param mixed   $product_id
     * @param Request $request
     * @return array
     */
    protected function requestToFilters($product_id, Request $request)
    {
        $filters = [];
        if (empty($product_id)) {
            if ($m = $request->get('master')){
                $filters['Master'] = trim($m);
            }

            if ($s = $request->get('size')){
                $filters['Size'] = trim($s);
            }

            if ($c = $request->get('color')){
                $filters['Color'] = trim($c);
            }
        }

        return $filters;
    }


    /**
     * the easy one, we have the exact product id, fetch it and return the status
     *
     * @param integer $product_id
     * @return Response
     */
    protected function exactProduct($product_id)
    {
        $translator = $this->get('translator');
        $product    = ProductsQuery::create()
            ->filterByRange($this->container->get('hanzo_product.range')->getCurrentRange())
            ->findOneById($product_id)
        ;

        if (!$product instanceof Products) {
            return $this->json_response([
                'message' => $translator->trans('No such product (#' . $product_id . ')'),
                'status'  => false,
            ], 400);
        }

        $result = $this->get('stock')->check($product);

        $data = [
            'status' => false,
            'message' => 'Product out of stock',
            'data' => [
                'color'      => $product->getColor(),
                'date'       => '',
                'master'     => $product->getMaster(),
                'product_id' => $product->getId(),
                'size'       => $product->getSize(),
                'size_label' => $product->getPostfixedSize($translator),
            ]];

        if ($result) {
            $data['status']       = true;
            $data['message']      = $translator->trans('Product in stock');
            $data['data']['date'] = ($result instanceof \DateTime ? $result->format('Y m/d') : '');

            return $this->json_response($data, 200);
        }

        return $this->json_response(['status' => false]);
    }


    /**
     * find products based on parameter filters
     *
     * @param array $filters
     *
     * @return Response
     */
    protected function filteredProduct(array $filters)
    {
        $translator = $this->get('translator');

        $query = ProductsQuery::create()
            ->filterByIsOutOfStock(false)
            ->filterByRange($this->container->get('hanzo_product.range')->getCurrentRange())
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
            ->endUse()
            // Be sure to order by size as a number(192) not text(192-198)
            ->withColumn('CONVERT(SUBSTRING_INDEX(products.SIZE,\'-\',1),UNSIGNED INTEGER)', 'size_num')
            ->orderBy('size_num')
            ->orderBy('color')
            ->groupById();

        $result = $query->findByArray($filters);
        if (!$result->count()) {
            return $this->json_response([
                'data'    => [],
                'message' => $translator->trans('No product(s) in stock.'),
                'status'  => false,
            ]);
        }

        $stock = $this->get('stock');
        $stock->prime($result);

        $data = [];
        $message = '';
        foreach ($result as $record) {
            $date = $stock->check($record);

            if (false === $date) {
                continue;
            }

            $date = ($date instanceof \DateTime ? $date->format('d.m.Y') : '');

            $data[] = [
                'color'      => $record->getColor(),
                'date'       => $date,
                'master'     => $record->getMaster(),
                'product_id' => $record->getId(),
                'size'       => $record->getSize(),
                'size_label' => $record->getPostfixedSize($translator),
            ];

            if ($date) {
                $message = $translator->trans('late.delivery', [
                    '%date%'    => $date,
                    '%product%' => $record->getMaster(),
                ], 'js');
            }
        }

        if (count($data)) {
            $data = ['products' => $data];
        }

        return $this->json_response([
            'data'    => $data,
            'message' => $message ? $message : '',
            'status'  => true,
        ]);
    }
}
