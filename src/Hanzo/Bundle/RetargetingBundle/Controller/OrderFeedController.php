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
        $ts = microtime(1);
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

        $response = new StreamedResponse();
        $response->setCallback(function() use ($from_date, $to_date, $that) {
            $that->streamFeed($from_date, $to_date);
        });

        $response->headers->add([
            'Content-type' => 'application/xml',
            'X-hanzo-m'    => Tools::humanReadableSize(memory_get_peak_usage()),
            'X-hanzo-t'    => (microtime(1) - $ts)

        ]);
        return $response->send();
    }

    private function streamFeed($from_date, $to_date)
    {
        echo '<?xml version="1.0" encoding="UTF-8"?><orders><since>'.$from_date->format('Y-m-d H:i:s').'</since><to>'.$to_date.'</to>';
        flush();

        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            $sql = "
                SELECT
                    o.id, o.customers_id, o.first_name, o.last_name, o.email, o.phone, o.currency_code, o.billing_title, o.billing_first_name, o.billing_last_name, o.billing_address_line_1, o.billing_address_line_2, o.billing_postal_code, o.billing_city, o.billing_country, o.billing_state_province, o.billing_company_name, o.delivery_title, o.delivery_first_name, o.delivery_last_name, o.delivery_address_line_1, o.delivery_address_line_2, o.delivery_postal_code, o.delivery_city, o.delivery_country, o.delivery_state_province, o.delivery_company_name, o.created_at,
                    orders_id, ol.type, ol.products_id, ol.products_sku, ol.products_name, ol.products_color, ol.products_size, ol.expected_at, ol.original_price, ol.price, ol.vat, ol.quantity, ol.unit
                FROM
                    orders AS o
                JOIN
                    orders_lines AS ol
                    ON
                      (ol.orders_id = o.id)
                WHERE
                    o.state > 30
                    AND
                      o.created_at > :created_at
            ";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue(':created_at', $from_date->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
            $stmt->execute();

            echo '<segment name="'.$this->connection_map[$name].'">';
            flush();

            $current_id = 0;
            while ($order = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                if ($order['id'] != $current_id) {
                    if ($current_id != 0) {
                        echo '</order_lines></order>';
                        flush();
                    }
                    $current_id = $order['id'];

                    echo '<order id="'.$order['id'].'"><customers_id>'.$order['customers_id'].'</customers_id><first_name>'.$order['first_name'].'</first_name><last_name>'.$order['last_name'].'</last_name><email>'.$order['email'].'</email><phone>'.$order['phone'].'</phone><currency_code>'.$order['currency_code'].'</currency_code><billing_title>'.$order['billing_title'].'</billing_title><billing_first_name>'.$order['billing_first_name'].'</billing_first_name><billing_last_name>'.$order['billing_last_name'].'</billing_last_name><billing_address_line_1>'.$order['billing_address_line_1'].'</billing_address_line_1><billing_address_line_2>'.$order['billing_address_line_2'].'</billing_address_line_2><billing_postal_code>'.$order['billing_postal_code'].'</billing_postal_code><billing_city>'.$order['billing_city'].'</billing_city><billing_country>'.$order['billing_country'].'</billing_country><billing_state_province>'.$order['billing_state_province'].'</billing_state_province><billing_company_name>'.$order['billing_company_name'].'</billing_company_name><delivery_title>'.$order['delivery_title'].'</delivery_title><delivery_first_name>'.$order['delivery_first_name'].'</delivery_first_name><delivery_last_name>'.$order['delivery_last_name'].'</delivery_last_name><delivery_address_line_1>'.$order['delivery_address_line_1'].'</delivery_address_line_1><delivery_address_line_2>'.$order['delivery_address_line_2'].'</delivery_address_line_2><delivery_postal_code>'.$order['delivery_postal_code'].'</delivery_postal_code><delivery_city>'.$order['delivery_city'].'</delivery_city><delivery_country>'.$order['delivery_country'].'</delivery_country><delivery_state_province>'.$order['delivery_state_province'].'</delivery_state_province><delivery_company_name>'.$order['delivery_company_name'].'</delivery_company_name><created_at>'.$order['created_at'].'</created_at><order_lines>';
                    flush();
                }

                echo '<line><type>'.$order['type'].'</type><products_sku>'.$order['products_sku'].'</products_sku><products_name>'.$order['products_name'].'</products_name><products_color>'.$order['products_color'].'</products_color><products_size>'.$order['products_size'].'</products_size><original_price>'.$order['original_price'].'</original_price><price>'.$order['price'].'</price><vat>'.$order['vat'].'</vat><quantity>'.$order['quantity'].'</quantity><unit>'.$order['unit'].'</unit></line>';
                flush();
            }

            echo '</order_lines></order></segment>';
            flush();
        }

        echo '</orders>';
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
