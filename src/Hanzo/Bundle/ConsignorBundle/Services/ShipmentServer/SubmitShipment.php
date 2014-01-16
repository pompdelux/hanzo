<?php

namespace Hanzo\Bundle\ConsignorBundle\Services\ShipmentServer;

use Hanzo\Bundle\ConsignorBundle\Consignor;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

class SubmitShipment
{
    private $consignor;

    /**
     * @var ConsignorAddress
     */
    private $from_address;

    /**
     * @var ConsignorAddress
     */
    private $to_address;

    /**
     * @var Integer
     */
    private $order_id;


    /**
     * Setup service
     *
     * @param Consignor $consignor
     */
    public function __construct(Consignor $consignor)
    {
        $this->consignor = $consignor;
    }


    /**
     * Set from address on the label
     *
     * @param ConsignorAddress $address
     */
    public function setFromAddress(ConsignorAddress $address)
    {
        $this->from_address = $address;
    }


    /**
     * Set to address on the label
     *
     * @param ConsignorAddress $address
     */
    public function setToAddress(ConsignorAddress $address)
    {
        $this->to_address = $address;
    }


    /**
     * @param integer $id
     */
    public function setOrderId($id)
    {
        $this->order_id = (int) $id;
    }


    /**
     * Send the Shipment and get the label in return.
     *
     * @return string PDF Shipping label as a string.
     * @throws \Exception
     */
    public function fetchReturnLabel()
    {
        if (empty($this->order_id)) {
            throw new MissingMandatoryParametersException("Mandatory paraneter 'order_id' not set.");
        }

        $address = $this->consignor->getOption('to_address');

        if ($address && isset($address['name'])) {
            $this->setToAddress(new ConsignorAddress(
                1,
                $address['name'],
                $address['address_line_1'],
                $address['address_line_2'],
                $address['postal_code'],
                $address['city'],
                $address['country_iso2'],
                $address['email'],
                $address['phone']
            ));
        }

        $data = [
            'command' => 'SubmitShipment',
            'actor'   => $this->consignor->getOption('actor'),
            'key'     => $this->consignor->getOption('key'),
            'options' => json_encode(['Labels' => 'PDF']),
            'data'    => json_encode([
                'Kind'          => 2,
                'OrderNo'       => $this->order_id,
                'ActorCSID'     => $this->consignor->getOption('actor'),
                'ProdConceptID' => $this->consignor->getOption('product_concept_id'),
                'Services'      => [$this->consignor->getOption('service_id')],
                'Addresses'     => [
                    $this->to_address->toArray(),
                    $this->from_address->toArray(),
                ],
                'Lines' => [[
                    'LineWeight' => 5000,
                    'PkgWeight'  => 5000,
                    'Pkgs'       => [
                        ['ItemNo' => 1]
                    ]
                ]]
            ])
        ];

        $request = $this->consignor->getGuzzleClient()->post(null, null, $data);

        // consignor's ssl cert is not ... hmm verified, so we skip verification
        $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYHOST, false);
        $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYPEER, false);

        $response = $request->send();

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Server responded with status code: '.$response->getStatusCode());
        }

        $json = $response->json();

        if (isset($json['ErrorMessages'])) {
            throw new \Exception(implode('\n', $json['ErrorMessages']));
        }

        return base64_decode($json['Labels'][0]['Content']);
    }
}
