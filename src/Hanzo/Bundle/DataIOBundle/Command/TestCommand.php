<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface
    ;

use Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Model\OrdersLines,
    Hanzo\Model\OrdersLinesPeer,
    Hanzo\Model\OrdersLinesQuery,
    Hanzo\Model\OrdersStateLog,
    Hanzo\Model\OrdersAttributes,
    Hanzo\Model\OrdersAttributesQuery,
    Hanzo\Model\OrdersVersions,
    Hanzo\Model\OrdersVersionsQuery,
    Hanzo\Model\ShippingMethods,
    Hanzo\Model\Products,
    Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsDomainsPrices,
    Hanzo\Model\ProductsDomainsPricesPeer,
    Hanzo\Model\ProductsDomainsPricesQuery,
    Hanzo\Model\ConsultantNewsletterDrafts,
    Hanzo\Model\GothiaAccounts,
    Hanzo\Model\GothiaAccountsQuery
    ;

use Exception;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:test')
            ->setDescription('For testing')
            ;
    }


    /**
     * -------------------------------------------------------------------->
     */
    /**
     * buildRequest
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function buildRequest()
    {
        $username = 'pompdelux_dk';
        $token = 'c4bfaa0026f352e13aab064ea623e6cec3703e64';
        $email = 'hf+mailplatform@bellcom.dk';

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
<xmlrequest>
    <username>'.$username.'</username>
    <usertoken>'.$token.'</usertoken>
    <requesttype>subscribers</requesttype>
    <requestmethod>GetSubscribers</requestmethod>
    <details>
        <searchinfo>
            <Email>
                <exactly>true</exactly>
                <data>'.$email.'</data>
            </Email>
        </searchinfo>
    </details>
</xmlrequest>';
        return $xml;
    }

    /**
     * executeRequest
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function executeRequest($request)
    {
        $baseURL = 'http://client2.mailmailmail.net/';

        $client = new \GuzzleHttp\Client( ['base_url' => $baseUrl] );

        //$ch = curl_init($application_URL .'/xml.php');
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        //return curl_exec($ch);


        $response = $client->post('xml.php', ['body' => $request]);

        return $response;
    }

    /**
     * <--------------------------------------------------------------------
     */

    /**
     * executes the job
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = $this->buildRequest();
        $result  = $this->executeRequest($request);
        $sxe = simplexml_load_string($result);

        echo $sxe->status.PHP_EOL;

        //$stock = $this->getContainer()->get('stock');
        //$stock->check(123);

        return;

        $redis = $this->getContainer()->get('pdl.phpredis.stock');
        //        $redis->hMset('products_id.123', ['2013-12-01' => 1,  'id' => '123']);
        //        $redis->hMset('products_id.123', ['2000-11-01' => 12, 'id' => '123']);
        //        $redis->hMset('products_id.123', ['2013-12-12' => 3,  'id' => '123']);

        $stock = [];
        $redis->multi();
        foreach ([123, 124] as $id) {
            $redis->hGetAll('products_id.'.$id);
        }

        foreach ($redis->exec() as $product) {
            $count = 1;
            $id = $product['id'];
            unset ($product['id']);
            $stock[$id] = [];
            foreach ($product as $date => $quantity) {
                $stock[$id][str_replace('-', '', $date)] = [
                    'id'       => $count++,
                    'date'     => $date,
                    'quantity' => $quantity,
                ];
            }
        }
        print_r($stock);
        return;
        foreach ($redis->exec() as $record) {
            $stock[$id] = [
                'total' => 0,
            ];
            $count = 1;
            foreach ($redis->hGetAll('products_id.'.$id) as $date => $quantity) {
                $date = str_replace('-', '', $date);
                $stock[$id][$date] = [
                    'id'       => $count,
                    'date'     => $date,
                    'quantity' => $quantity,
                ];
                $stock[$id]['total'] += $quantity;

                $count++;
            }
        }

        print_r($stock);

        //        $soap = new \SoapClient('http://pdl.un/da_DK/soap/v1/ECommerceServices/?wsdl');
        //        $soap->__setLocation('http://pdl.un/da_DK/soap/v1/ECommerceServices/');
        //        //print_r($soap->__getFunctions());
        //        //
        //
        //        $data = new \stdClass();
        //        $data->eOrderNumber = 1013569;
        //        $data->amount = -10.00;
        //        $data->initials = 'un';
        //        $result = $soap->SalesOrderCaptureOrRefund($data);
        //
        //
        //        print_r($result);

        // $accounts = GothiaAccountsQuery::create()
        //     ->find();

        // foreach ($accounts as $account)
        // {
        //     $account->setExternalId( $account->getCustomersId() );
        //     $account->save();
        // }

        /*$accounts = GothiaAccountsQuery::create()
            ->find();

        foreach ($accounts as $account)
        {
            $ssn = $account->getSocialSecurityNum();
            $newssn = $ssn;
            if ( strlen($ssn) > 10 && substr($ssn,0,2) == 19)
            {
                $newssn = substr( $ssn, 2 );
                echo $ssn.' => '.$newssn."\n";
                $account->setSocialSecurityNum($newssn);
                $account->save();
            }
        }*/


        /*$missingNames = array(
        );

        foreach ($missingNames as $orderId)
        {
            $order     = OrdersPeer::retrieveByPK($orderId);
            $customer  = $order->getCustomers();
            $addresses = $customer->getAddresses();

            foreach ($addresses as $address)
            {
                $address->setFirstName( $order->getFirstName() );
                $address->setLastName( $order->getLastName() );
                $address->save();

                switch ($address->getType())
                {
                    case 'payment':
                        $order->setBillingFirstName( $address->getFirstName() );
                        $order->setBillingLastName( $address->getLastName() );
                        break;
                    case 'shipping':
                        $order->setDeliveryFirstName( $address->getFirstName() );
                        $order->setDeliveryLastName( $address->getLastName() );
                        break;

                }
            }

            $order->save();
        }*/

        /*$order = OrdersPeer::retrieveByPK(759830);

        echo $order->getTotalPrice()."\n";
        echo $order->getTotalVat()."\n";*/

        /*$draft = new ConsultantNewsletterDrafts();
        $draft
            ->setSubject('Test')
            ->setContent('Hest')
            ->setConsultantsId(2000)
        ->save();*/

        /*$order = new Orders();
        $order->setAttribute( 'transact', 'payment', '596022444' );

        $gateway = $this->getContainer()->get('payment.dibsapi');

        $settings['merchant'] = '90052482';
        $settings['md5key1']  = 'Y[?Eh|QAA?&PPwwDB[CalMSHaQ.M?CKz';
        $settings['md5key2']  = '8IBaYSmjDLkZz.+hKhNtcb]~XikRAqFF';
        $settings['api_user'] = 'pdl-dk-api-user';
        $settings['api_pass'] = 'D!An6aYlUf*l';

        $gateway->mergeSettings($settings);

        print_r($gateway->call()->payinfo($order));*/


        /*$prices = ProductsDomainsPricesQuery::create()
            ->filterByProductsId( array(1) )
            ->filterByDomainsId( 1)
            ->orderByProductsId()
            ->find()
        ;

        foreach ($prices as $price)
        {
          $vat = ( $price->getPrice() * 1.25 ) - $price->getPrice();
          $price->setVat( number_format( $vat, 2, '.', '' ) );
          $price->save();
        }*/

        /*$order = OrdersPeer::retrieveByPK(572871);

        $gateway = $this->get('payment.dibsapi');

        $settings = $gateway->getSettings();
        $settings['merchant'] = '90057323';
        $settings['md5key1']  = 'd|y3,Wxe5dydME)q4+0^BilEVfT[WuSp';
        $settings['md5key2']  = 'Q+]FJ]0FMvsyT,_GEap39LlgIr1Kx&n[';
        $settings['api_user'] = 'bellcom_test_api_user';
        $settings['api_pass'] = '7iuTR8EZ';

        $call = DibsApiCall::getInstance($settings, $gateway);
        $response = $call->capture($order, $amount);

        print_r($response);*/
    }
}
