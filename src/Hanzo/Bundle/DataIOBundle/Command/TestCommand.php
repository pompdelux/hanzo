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
     * executes the job
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accounts = GothiaAccountsQuery::create()
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
        }


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
