<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\CoreController;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\OrdersLinesQuery,
    Hanzo\Model\DomainsQuery,
	Hanzo\Model\Orders;

class StatisticsController extends CoreController
{
    public function indexAction($domain_key)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $date_filter = array();
    	if (!empty($_GET['select-periode'])) { // Select input from form

    		switch ($this->getRequest()->get('select-periode', null)) {
    			case 'thisweek':
    				$date_filter['min'] = date('d-m-Y',strtotime('Monday this week'));
		            $start = $date_filter['min'];
    				$date_filter['max'] = date('d-m-Y',strtotime('Sunday this week'));
		            $end = $date_filter['max'];
    				break;
    			case 'thismonth':
    				$date_filter['min'] = date('d-m-Y',strtotime('first day of this month'));
		            $start = $date_filter['min'];
		            $date_filter['max'] = date('d-m-Y',strtotime('last day of this month'));
		            $end = $date_filter['max'];
    				break;
    		}

    	}elseif (isset($_GET['start']) && isset($_GET['end'])) {
            $start = $this->getRequest()->get('start', null);
            $end = $this->getRequest()->get('end', null);

            $date_filter['min'] = strtotime($start);
            //$date_filter['max'] = strtotime($end);
            $date_filter['max'] = strtotime(date("Y-m-d", strtotime($end)) . " +1 day");
        }else{
        	// Default period is TODAY
            $date_filter['min'] = date('d-m-Y', time());
            $start = $date_filter['min'];
            $date_filter['max'] = $date_filter['min'];
            $end = $date_filter['max'];

        }

    	$orders_amount = OrdersLinesQuery::create()
    		->filterByType('product')
    		->withColumn('SUM(orders_lines.quantity)','TotalProducts')
    		->useOrdersQuery()
    			->withColumn('COUNT( DISTINCT orders.id)','TotalOrders')
    			->withColumn('DATE(orders.createdAt)','CreatedAt')
    			->filterByCreatedAt($date_filter)
                ->filterByState(array('min' => Orders::STATE_PENDING))
    		->endUse()
    	;

    	if($domain_key){
    		$orders_amount = $orders_amount
    			->useOrdersQuery()
    				->useOrdersAttributesQuery()
    					->filterByCKey('domain_key')
    					->filterByCValue($domain_key)
    				->endUse()
    				->joinOrdersAttributes()
    			->endUse()
    		;
    	}
    	$orders_amount = $orders_amount
    		->select(array('CreatedAt', 'TotalProducts', 'TotalOrders'))
    		->groupBy('CreatedAt')
    		->orderBy('CreatedAt')
    		->find()
    	;

        $orders_price = OrdersLinesQuery::create()
            ->withColumn('SUM(orders_lines.price)','TotalPrice')
            ->useOrdersQuery()
                ->withColumn('DATE(orders.createdAt)','CreatedAt')
                ->filterByCreatedAt($date_filter)
                ->filterByState(array('min' => Orders::STATE_PENDING))
            ->endUse()
        ;
        if($domain_key){
            $orders_price = $orders_price
                ->useOrdersQuery()
                    ->useOrdersAttributesQuery()
                        ->filterByCKey('domain_key')
                        ->filterByCValue($domain_key)
                    ->endUse()
                    ->joinOrdersAttributes()
                ->endUse()
            ;
        }
        $orders_price = $orders_price
            ->select(array('CreatedAt', 'TotalPrice'))
            ->groupBy('CreatedAt')
            ->orderBy('CreatedAt')
            ->find()
        ;

        // Build the array for every day with orders and the array for the sum.
        $orders_array = array();
        $orders_total = array(
            'sumprice' => 0,
            'sumorders' => 0,
            'sumproducts' => 0
        );
        foreach ($orders_amount as $order) {
            $orders_array[$order['CreatedAt']]['TotalProducts'] = $order['TotalProducts']; 
            $orders_array[$order['CreatedAt']]['TotalOrders'] = $order['TotalOrders'];
            $orders_array[$order['CreatedAt']]['CreatedAt'] = $order['CreatedAt'];

            $orders_total['sumorders'] += $order['TotalOrders'];
            $orders_total['sumproducts'] += $order['TotalProducts'];
        }
        foreach ($orders_price as $order) {
            $orders_array[$order['CreatedAt']]['TotalPrice'] = $order['TotalPrice'];

            $orders_total['sumprice'] += $order['TotalPrice'];
        }

		$domains_availible = DomainsQuery::Create()
			->find()
		;
        
        return $this->render('AdminBundle:Statistics:index.html.twig', array(
            'orders_array' => $orders_array,
            'total' => $orders_total,
            'domain_key' => $domain_key,
            'domains_availible' => $domains_availible,
            'start' => $start,
            'end' => $end
        ));
    }
}
