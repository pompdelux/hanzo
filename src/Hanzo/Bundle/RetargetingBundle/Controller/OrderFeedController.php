<?php

namespace Hanzo\Bundle\RetargetingBundle\Controller;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @Route("/retarteging/order-feed/{since}", defaults={"_format"="xml", "since"="-1 month"})
     *
     * @param  Request $request
     * @param  string $since
     * @throws AccessDeniedException
     * @return Response
     */
    public function orderFeedAction(Request $request, $since)
    {
// disabled for now - we rely on "basic auth" - maybe this will change in the future ?
//        if (!in_array($request->getClientIp(), ['185.14.184.152', '95.166.153.185'])) {
//            Tools::log('Access denied for '.$request->getClientIp().' to '.__METHOD__);
//            throw new AccessDeniedException('You do not have access to this area.');
//        }

        try {
            $from_date = new \DateTime($since);
            $to_date   = date('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return new Response("'since' not in a valid format.", 500);
        }

        $orders = [];
        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            $result = OrdersQuery::create()
                ->filterByState(30, \Criteria::GREATER_THAN)
                ->filterByCreatedAt($from_date, \Criteria::GREATER_THAN)
                ->joinWithOrdersLines()
                ->find($connection)
            ;
    
            foreach ($result as $order) {
                $orders[$this->connection_map[$name]][] = $order;
            }
        }

        return $this->render('RetargetingBundle:OrderFeed:feed.xml.twig', [
            'since' => $from_date->format('Y-m-d H:i:s'),
            'to'    => $to_date,
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
