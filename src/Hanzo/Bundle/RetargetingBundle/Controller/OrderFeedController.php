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
use Symfony\Component\HttpFoundation\StreamedResponse;
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

        ob_flush();
        set_time_limit(0);
        $that = $this;

        $response = new StreamedResponse(null, 200, ['Content-type' => 'application/xml']);
        $response->setCallback(function() use ($from_date, $to_date, $that) {
            $that->streamFeed($from_date, $to_date);
        });

        return $response->send();
    }

    private function streamFeed($from_date, $to_date)
    {
        echo '<?xml version="1.0" encoding="UTF-8"?><Orders><Since>'.$from_date->format('Y-m-d H:i:s').'</Since><To>'.$to_date.'</To>';
        flush();

        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            $result = OrdersQuery::create()
                ->filterByState(30, \Criteria::GREATER_THAN)
                ->filterByCreatedAt($from_date, \Criteria::GREATER_THAN)
                ->joinWithOrdersLines()
                ->find($connection)
            ;

            echo '<Segment name="'.$this->connection_map[$name].'">';
            flush();

            $first = true;
            foreach ($result as $order) {
                if ($first) {
                    echo '<Order id="'.$order->getId().'"><CustomersId>'.$order->getCustomersId().'</CustomersId><FirstName>'.$order->getFirstName().'</FirstName><LastName>'.$order->getLastName().'</LastName><Email>'.$order->getEmail().'</Email><Phone>'.$order->getPhone().'</Phone><CurrencyCode>'.$order->getCurrencyCode().'</CurrencyCode><BillingTitle>'.$order->getBillingTitle().'</BillingTitle><BillingFirstName>'.$order->getBillingFirstName().'</BillingFirstName><BillingLastName>'.$order->getBillingLastName().'</BillingLastName><BillingAddressLine1>'.$order->getBillingAddressLine1().'</BillingAddressLine1><BillingAddressLine2>'.$order->getBillingAddressLine2().'</BillingAddressLine2><BillingPostalCode>'.$order->getBillingPostalCode().'</BillingPostalCode><BillingCity>'.$order->getBillingCity().'</BillingCity><BillingCountry>'.$order->getBillingCountry().'</BillingCountry><BillingStateProvince>'.$order->getBillingStateProvince().'</BillingStateProvince><BillingCompanyName>'.$order->getBillingCompanyName().'</BillingCompanyName><DeliveryTitle>'.$order->getDeliveryTitle().'</DeliveryTitle><DeliveryFirstName>'.$order->getDeliveryFirstName().'</DeliveryFirstName><DeliveryLastName>'.$order->getDeliveryLastName().'</DeliveryLastName><DeliveryAddressLine1>'.$order->getDeliveryAddressLine1().'</DeliveryAddressLine1><DeliveryAddressLine2>'.$order->getDeliveryAddressLine2().'</DeliveryAddressLine2><DeliveryPostalCode>'.$order->getDeliveryPostalCode().'</DeliveryPostalCode><DeliveryCity>'.$order->getDeliveryCity().'</DeliveryCity><DeliveryCountry>'.$order->getDeliveryCountry().'</DeliveryCountry><DeliveryStateProvince>'.$order->getDeliveryStateProvince().'</DeliveryStateProvince><DeliveryCompanyName>'.$order->getDeliveryCompanyName().'</DeliveryCompanyName><CreatedAt>'.$order->getCreatedAt().'</CreatedAt><OrderLines>';
                    flush();
                }

                foreach ($order->getOrdersLiness() as $line) {
                    echo '<Line><Type>'.$line->getType().'</Type><ProductsSku>'.$line->getProductsSku().'</ProductsSku><ProductsName>'.$line->getProductsName().'</ProductsName><ProductsColor>'.$line->getProductsColor().'</ProductsColor><ProductsSize>'.$line->getProductsSize().'</ProductsSize><OriginalPrice>'.$line->getOriginalPrice().'</OriginalPrice><Price>'.$line->getPrice().'</Price><Vat>'.$line->getVat().'</Vat><Quantity>'.$line->getQuantity().'</Quantity><Unit>'.$line->getUnit().'</Unit></Line>';
                    flush();
                }

                if ($first) {
                    echo '</OrderLines></Order>';
                    flush();
                }

                $first = false;
            }

            echo '</Segment>';
            flush();
        }

        echo '</Orders>';
        flush();
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
