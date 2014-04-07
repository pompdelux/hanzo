<?php

namespace Hanzo\Bundle\AxBundle\Command;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SoapInGiftCardTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:ax:test-create-giftcard')
            ->setDescription('Allows us to test creation of giftcards.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // product
        $InventTable = new \stdClass();
        $InventTable->ItemGroupId     = "G_Access,LG_Access";
        $InventTable->ItemGroupName   = "Girl Accessories";
        $InventTable->ItemId          = 'TestGiftCardSS14';
        $InventTable->WebEnabled      = true;
        $InventTable->ItemName        = 'Gavekort 200,-';
        $InventTable->ItemType        = "Item";
        $InventTable->NetWeight       = 0.00;
        $InventTable->BlockedDate     = "1990-01-01";
        $InventTable->WashInstruction = null;
        $InventTable->IsVoucher       = true;
        $InventTable->WebDomain       = ['COM', 'DK', 'SalesDK'];

        $InventTable->Sales = (object) [
            "Price"  => 0.00,
            "UnitId" => "Stk.",
        ];

        $InventTable->InventDim = [
            (object) [
                "InventColorId" => 'Blue',
                "InventSizeId"  => '200,-',
            ],
        ];

        $data = new \stdClass();
        $data->item = new \stdClass();
        $data->item->InventTable = $InventTable;

        $client = new \SoapClient('http://pdl.un/da_DK/soap/v1/ECommerceServices/?wsdl', [
            'trace' => true
        ]);
        $client->__setLocation('http://pdl.un/da_DK/soap/v1/ECommerceServices/');

        $result = $client->SyncItem($data);
        print_r($result);

        // Pris sync
        $priceList = new \stdClass();
        $priceList->ItemId = 'TestGiftCardSS14';
        $priceList->SalesPrice = [
            (object) [
                'AmountCur'     => 200.00,
                'Currency'      => 'DKK',
                'CustAccount'   => 'DKK',
                'InventColorId' => 'Blue',
                'InventSizeId'  => '200,-',
                'PriceUnit'     => 1.00,
                'Quantity'      => 1.00,
                'UnitId'        => 'Stk.',
            ],
            (object) [
                'AmountCur'     => 200.00,
                'Currency'      => 'EUR',
                'CustAccount'   => 'EUR',
                'InventColorId' => 'Blue',
                'InventSizeId'  => '200,-',
                'PriceUnit'     => 1.00,
                'Quantity'      => 1.00,
                'UnitId'        => 'Stk.',
            ],
        ];

        $data = new \stdClass();
        $data->priceList = $priceList;

        $result = $client->SyncPriceList($data);
        print_r($result);

        // inventory
        $inventoryOnHand = (object) [
            'InventSum' => (object) [
                'ItemId'      => 'TestGiftCardSS14',
                'LastInCycle' => false,
                'InventDim'   => [
                    (object) [
                        'InventColorId'             => 'Blue',
                        'InventSizeId'              => '200,-',
                        'InventQtyAvailOrdered'     => '',
                        'InventQtyAvailOrderedDate' => '',
                        'InventQtyAvailPhysical'    => '100',
                    ]
                ]
            ],
        ];

        $data = new \stdClass();
        $data->inventoryOnHand = $inventoryOnHand;
        $result = $client->SyncInventoryOnHand($data);
        print_r($result);
    }
}
