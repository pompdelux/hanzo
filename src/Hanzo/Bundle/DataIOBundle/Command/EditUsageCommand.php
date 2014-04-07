<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Hanzo\Model\OrdersQuery;
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

class EditUsageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:edit-usage-stats')
            ->setDescription('For testing')
        ;
    }

    /**
     * executes the job
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orders = OrdersQuery::create()
            ->filterByVersionId(1, \Criteria::GREATER_THAN)
            ->filterByCreatedAt(strtotime('2014-02-20 00:00:01'), \Criteria::GREATER_THAN)
            ->filterByCreatedAt(strtotime('2014-03-17 00:00:01'), \Criteria::LESS_THAN)
            ->filterByState(Orders::STATE_PENDING, \Criteria::GREATER_EQUAL)
            ->filterByCurrencyCode('DKK')
            ->find()
        ;

        file_put_contents('/tmp/os.csv', "'created at';'original total';'last update at';'current total'\r\n");
        foreach ($orders as $order) {
            /** @var Orders $order */
            /** @var Orders $old_version */

            $old_version = unserialize(OrdersVersionsQuery::create()
                ->filterByOrdersId($order->getId())
                ->orderByVersionId(\Criteria::DESC)
                ->limit(1)
                ->find()
                ->getFirst()->getContent()
            );

            $old_total = 0;
            foreach ($old_version['products'] as $line) {
                $old_total += ($line['Price'] * $line['Quantity']);
            }

            $data = $order->getId().';'
                . "'".$old_version['order']['CreatedAt']."';"
                . number_format($old_total, 2, ',', '').';'
                . "'".$order->getUpdatedAt('Y-m-d H:i:s')."';"
                . number_format($order->getTotalPrice(), 2, ',', '')."\r\n"
            ;
            file_put_contents('/tmp/os.csv', $data, FILE_APPEND);
        }

        $mailer = $this->getContainer()->get('mail_manager');
        $mailer->setTo(['hd@pompdelux.dk','un@bellcom.dk']);
        $mailer->setFrom('mail@pompdelux.dk');
        $mailer->setSubject('et stk ordrefil');
        $mailer->setBody("Hej der,\n\nSå er der tal at se på..\n\n-- \nmvh robotten\n");
        $mailer->addAttachment('/tmp/os.csv');
        $mailer->send();
    }
}
