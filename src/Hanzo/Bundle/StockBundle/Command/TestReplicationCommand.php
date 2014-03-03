<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\StockBundle\Command;

use Hanzo\Bundle\AdminBundle\Event\FilterCategoryEvent;
use Hanzo\Model\ProductsQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestReplicationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:stock:test-replication')
            ->setDescription('Some test thingy.')
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
        $this->getContainer()->get('event_dispatcher')->dispatch('product.stock.zero', new FilterCategoryEvent(ProductsQuery::create()->findOneById(63), 'da_DK'));
return;
        $replicator = $this->getContainer()->get('hanzo.propel_replicator');

        $results = $replicator->executeQuery("
            SELECT
                cms_i18n.settings,
                CONCAT('/', cms_i18n.locale, '/', cms_i18n.path) as path
            FROM
                cms
            JOIN
                cms_i18n
            ON (
                cms.id = cms_i18n.id
            )
            WHERE
                cms.type = 'category'
        ");

        $category_map = [];
        foreach ($results as $name => $sth) {
            echo $name."\n";
            while ($record = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $settings = json_decode($record['settings']);
                if (empty($category_map[$settings->category_id])) {
                    $category_map[$settings->category_id] = [];
                }

                if (!in_array($record['path'], $category_map[$settings->category_id])) {
                    $category_map[$settings->category_id][] = $record['path'];
                }
            }
        }

        print_r($category_map);
    }
}
