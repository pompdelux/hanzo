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
    Hanzo\Model\ShippingMethods
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
        $order = OrdersPeer::retrieveByPK(572871);

        $gateway = $this->get('payment.dibsapi');

        $settings = $gateway->getSettings();
        $settings['merchant'] = '90057323';
        $settings['md5key1']  = 'd|y3,Wxe5dydME)q4+0^BilEVfT[WuSp';
        $settings['md5key2']  = 'Q+]FJ]0FMvsyT,_GEap39LlgIr1Kx&n[';
        $settings['api_user'] = 'bellcom_test_api_user';
        $settings['api_pass'] = '7iuTR8EZ';

        $call = DibsApiCall::getInstance($settings, $gateway);
        $response = $call->capture($order, $amount);

        print_r($response);
    }
}
