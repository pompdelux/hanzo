<?php

namespace Hanzo\Bundle\RetargetingBundle\Controller;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class OrderFeedController extends Controller
{
    private $connections = [];

    private $connection_map = [
        'default'  => 'DK',
        'pdldbde1' => 'DE',
        'pdldbfi1' => 'FI',
        'pdldbnl1' => 'NL',
        'pdldbno1' => 'NO',
        'pdldbse1' => 'SE',
        'pdldbat1' => 'AT',
        'pdldbch1' => 'CH',
    ];

    /**
     * Fetches all orders since: $since.
     *
     * @Route("/retarteging/order-feed/{since}", defaults={"_format"="xml", "since"="20130101"})
     * @param string $since
     * @return Response
     */
    public function orderFeedAction($since)
    {
        try {
            $date = new \DateTime($since);
        } catch (\Exception $e) {
            return new Response("'since' not in a valid format.", 500);
        }

        $orders = [];
        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            $result = OrdersQuery::create()
                ->filterByState(30, \Criteria::GREATER_THAN)
                ->filterByCreatedAt($date, \Criteria::GREATER_THAN)
                ->joinWithOrdersLines()
                ->find($connection)
            ;

            foreach ($result as $order) {
                $orders[$this->connection_map[$name]][] = $order;
            }
        }

        return $this->render('RetargetingBundle:OrderFeed:feed.xml.twig', [
            'since' => $date->format('Y-m-d H:i:s'),
            'to'    => date('Y-m-d H:i:s'),
            'data'  => $orders,
        ]);
    }


    /**
     * Find active propel connections.
     *
     * @return array
     */
    protected function getConnections()
    {
        if (!$this->connections) {
            $this->findConnections();
        }

        return $this->connections;
    }


    /**
     * Get named Propel connection
     *
     * @param  string $name Name of connection to retrieve
     * @return PropelPDO    Propel connection object
     */
    protected function getConnection($name = 'default')
    {
        if (!$this->connections) {
            $this->findConnections();
        }

        if (empty($this->connections[$name])) {
            $this->connections[$name] = \Propel::getConnection($name);
        }

        return isset($this->connections[$name])
            ? $this->connections[$name]
            : null
        ;
    }


    /**
     * Parse Propel configuration and find connections.
     */
    private function findConnections()
    {
        foreach ($this->container->get('propel.configuration')->getFlattenedParameters() as $key => $value) {
            list($namespace, $name, $rest) = explode('.', $key, 3);

            // only add one connection, and only if the user is set
            if (($rest      == 'connection.user') &&
                ($namespace == 'datasources')
            ) {
                $value = trim($value);

                if (!empty($value) && empty($this->connections[$name])) {
                    $this->connections[$name] = null;
                    continue;
                }
            }
        }
    }
}
