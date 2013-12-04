<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Propel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\DomainsQuery;
use Hanzo\Model\Orders;

class StatisticsController extends CoreController
{
    public function indexAction($domain_key)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_STATS") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $date_filter = array();
        if (!empty($_GET['select-periode'])) { // Select input from form

            switch ($this->getRequest()->get('select-periode', null)) {
                case 'thisweek':
                    $date_filter['min'] = date('d-m-Y',strtotime('Monday this week'));
                    $start = $date_filter['min'];
                    $date_filter['max'] = date('d-m-Y',strtotime('Monday next week'));
                    $end = $date_filter['max'];
                    break;
                case 'thismonth':
                    $date_filter['min'] = date('d-m-Y',strtotime('first day of this month'));
                    $start = $date_filter['min'];
                    $date_filter['max'] = date('d-m-Y',strtotime('first day of next month'));
                    $end = $date_filter['max'];
                    break;
            }

        }elseif (isset($_GET['start']) && isset($_GET['end'])) {
            $start = $this->getRequest()->get('start', null);
            $end = $this->getRequest()->get('end', null);

            $date_filter['min'] = strtotime($start);
            //$date_filter['max'] = strtotime($end);
            $date_filter['max'] = strtotime(date("d-m-Y", strtotime($end)) . " +1 day");
        }else{
            // Default period is TODAY
            $date_filter['min'] = date('d-m-Y', time());
            $start = $date_filter['min'];
            $date_filter['max'] = $date_filter['min'];
            $end = $date_filter['max'];

        }


        // Build the array for every day with orders and the array for the sum.
        $orders_array = array();
        $orders_total = array(
            'sumprice' => 0,
            'sumorders' => 0,
            'sumproducts' => 0
        );
        $orders_amount = null;
        $orders_price = null;

        if($domain_key){
            $orders_amount = OrdersLinesQuery::create()
                ->filterByType('product')
                ->withColumn('SUM(orders_lines.quantity)','TotalProducts')
                ->useOrdersQuery()
                    //->withColumn('COUNT( DISTINCT orders.id)','TotalOrders')
                    ->withColumn('DATE(orders.createdAt)','CreatedAt')
                    ->filterByCreatedAt($date_filter)
                    ->filterByState(array('min' => Orders::STATE_PENDING))
                    ->useOrdersAttributesQuery()
                        ->filterByNs('global')
                        ->filterByCKey('domain_key')
                        ->filterByCValue($domain_key)
                    ->endUse()
                    ->joinOrdersAttributes()
                ->endUse()
                ->select(array('CreatedAt', 'TotalProducts', 'Orders.Id', 'OrdersAttributes.CValue'))
                ->groupBy('OrdersId')
                ->orderBy('CreatedAt')
                ->find($this->getDbConnection())
            ;

            $orders_price = OrdersLinesQuery::create()
                ->withColumn('SUM(orders_lines.price)','TotalPrice')
                ->useOrdersQuery()
                    ->withColumn('DATE(orders.createdAt)','CreatedAt')
                    ->filterByCreatedAt($date_filter)
                    ->filterByState(array('min' => Orders::STATE_PENDING))
                ->endUse()
                ->useOrdersQuery()
                    ->useOrdersAttributesQuery()
                        ->filterByNs('global')
                        ->filterByCKey('domain_key')
                        ->filterByCValue($domain_key)
                    ->endUse()
                    ->joinOrdersAttributes()
                ->endUse()
                ->select(array('CreatedAt', 'TotalPrice', 'Orders.CurrencyCode'))
                ->groupBy('CreatedAt')
                ->orderBy('CreatedAt')
                ->find($this->getDbConnection())
            ;

            foreach ($orders_amount as $order) {
                if(!isset($orders_array[$order['CreatedAt']]))
                    $orders_array[$order['CreatedAt']] = array(
                        'TotalProducts' => 0,
                        'TotalOrders' => 0,
                        'CreatedAt' => $order['CreatedAt']
                    );
                $orders_array[$order['CreatedAt']]['TotalProducts'] += $order['TotalProducts'];
                $orders_array[$order['CreatedAt']]['TotalOrders'] += 1;

                $orders_total['sumorders'] += 1;
                $orders_total['sumproducts'] += $order['TotalProducts'];
            }

            foreach ($orders_price as $order) {
                if(!$domain_key && $order['Orders.CurrencyCode'] == 'EUR'){
                    $orders_array[$order['CreatedAt']]['TotalPrice'] = $order['TotalPrice'] * 7.5;
                    $orders_total['sumprice'] += $order['TotalPrice'] * 7.5;
                }else{
                    $orders_array[$order['CreatedAt']]['TotalPrice'] = $order['TotalPrice'];
                    $orders_total['sumprice'] += $order['TotalPrice'];
                }

            }
        }

        $domains_availible = DomainsQuery::Create()
            ->find($this->getDbConnection())
        ;

        return $this->render('AdminBundle:Statistics:index.html.twig', array(
            'orders_array' => $orders_array,
            'total' => $orders_total,
            'domain_key' => $domain_key,
            'domains_availible' => $domains_availible,
            'start' => $start,
            'end' => $end,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }


    /**
     * generates realtime order stats
     *
     * @param  Request $request
     * @return Response
     */
    public function realtimeAction(Request $request)
    {
        $sources = [];
        foreach ($this->get('propel.configuration')->getFlattenedParameters() as $key => $value) {
            list($namespace, $name, $rest) = explode('.', $key, 3);

            // only add one connection, and only if the user is set
            if (($rest == 'connection.user') &&
                ($namespace == 'datasources')
            ) {
                $value = trim($value);
                if (!empty($value) && empty($sources[$name])) {
                    $sources[$name] = $name;
                    continue;
                }
            }
        }

        $sql = "
            SELECT
                DATE_FORMAT(created_at, '%Y-%m-%d %H:00') AS y,
                COUNT(*) AS a
            FROM
                orders
            WHERE
                created_at >= (now() - INTERVAL 1 DAY)
                AND
                  state > 20
            GROUP BY
                DAY(created_at),
                HOUR(created_at)
            ORDER BY
                y DESC
        ";

        $data = [];
        $index = 0;
        foreach ($sources as $db_name) {
            $con = Propel::getConnection($db_name, Propel::CONNECTION_WRITE);

            $stmt = $con->prepare($sql);
            $stmt->execute();

            if ($db_name == 'default') {
                $db_name = 'pdldbdk1';
            }

            $data[$index] = [
                'element' => $db_name,
                'xkey' => 'y',
                'ykeys' => ['a'],
                'labels' => ['Antal Order'],
            ];
            while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $data[$index]['data'][] = [
                    'y' => $record['y'],
                    'a' => $record['a'],
                ];
            }

            if (empty($data[$index]['data'])) {
                unset($data[$index]);
            } elseif ($this->getFormat() != 'json') {
                $data[$index]['data'] = json_encode($data[$index]);
            }

            $index++;
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response($data);
        }

        return $this->render('AdminBundle:Statistics:realtime.html.twig', [
            'data' => $data,
        ]);
    }
}
