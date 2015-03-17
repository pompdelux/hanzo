<?php

namespace Hanzo\Bundle\GoogleBundle\DataLayer;

class Ecommerce extends AbstractDataLayer
{
    public function __construct($page_type = '', Array $context = [], Array $params = [])
    {
        if ($page_type != 'checkout-success') {
            return;
        }

        if (!isset($context['order'])) {
            return;
        }

        $order = $context['order'];

        $purchase = [];
        $purchase['actionField'] = [
            'id'         =>  $order['id'],
            'affiliation'=>  $order['store_name'],
            'revenue'    =>  $order['total'],
            'shipping'   =>  $order['shipping'],
            'tax'        =>  $order['tax'],
            'currency'   =>  $order['currency'],
        ];

        $purchase['products'] = [];
        foreach ($order['lines'] as $line) {
            $product = [
                'id'       => $order['id'],
                'name'     => $line['name'],
                'sku'      => $line['sku'],
                'category' => $line['variation'],
                'price'    => $line['price'],
                'quantity' => $line['quantity'],
            ];

            $purchase['products'][] = $product;
        }

        $this->data = ['ecommerce' => [ 'purchase' => $purchase ]];
    }
}
