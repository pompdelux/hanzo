<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Propel;
use PDO;

use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsDomainsPrices;
use Hanzo\Model\ProductsDomainsPricesQuery;
use Hanzo\Model\ProductsI18n;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\ProductsImagesPeer;
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesProductReferencesPeer;
use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsPeer;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStockPeer;
use Hanzo\Model\ProductsStock;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsToCategoriesPeer;
use Hanzo\Model\ProductsToCategories;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\ProductsWashingInstructionsPeer;
use Hanzo\Model\ProductsWashingInstructions;
use Hanzo\Model\ProductsWashingInstructionsQuery;

use Hanzo\Model\DomainsQuery;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\LanguagesQuery;

use \Exception;
use \PropelCollection;

class ExportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:export')
            ->setDescription('Exports stuff')
            ->addArgument('export_type', InputArgument::REQUIRED, 'What to import')
            ->addArgument('database', InputArgument::REQUIRED, 'Which database to export from')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $export_type = $input->getArgument('export_type');
        $database   = $input->getArgument('database');

        //TODO: handle .se/.no/.nl/.com
        switch ($database) {
            case 'dk':
                $this->connection = Propel::getConnection( 'pdlfront_dk' );
                break;
            default:
                $this->output->writeln('<error>Unknown database "'.$database.'"</error>');
                exit;
                break;
        }

        //$this->connection->exec("SET NAMES latin1");

        switch ($export_type) {
            case 'products':
                $this->products_export();
                break;
            default:
                $this->output->writeln('<error>Unknown import type "'.$export_type.'"</error>');
                exit;
                break;
        }

        $this->output->writeln("\n".'<info>Export completed</info>');
    }


    public function products_export()
    {
        $now = date('Ymd');
        $products = ProductsQuery::create()
            //->filterByMaster('Abby SKIRT')
            //->filterByMaster('Jeff LS SHIRT')
            ->joinWithProductsToCategories()
            ->joinWithProductsDomainsPrices()
            ->joinWithProductsStock()
            ->where('products.MASTER = products.SKU')
            ->orderById()
            ->find()
        ;

ob_start();
echo '<?php';
?>

$client = new SoapClient( "http://ph.dk/app_dev.php/soap/v1/ECommerceServices/?wsdl", array(
  'trace'      => true,
  'exceptions' => true,
));
$client->__setLocation('http://ph.dk/app_dev.php/soap/v1/ECommerceServices/');

<?php

        $i = 0;
        $master = '';
        $split_count = $all = 0;
        foreach ($products as $product) {
            $split_count++;

            $categories = array();
            foreach ($product->getProductsToCategoriess() as $category) {
                $categories[] = $category->getCategories()->getContext();
            }

?>
// <?php echo $product->getMaster() . ' : ' . $all ."\n" ?>
$item = new stdClass();
$price = new stdClass();
$stock = new stdClass();

$item->item->InventTable->ItemId = '<?php echo $product->getMaster() ?>';
$price->priceList->ItemId = '<?php echo $product->getMaster() ?>';
$stock->inventoryOnHand->InventSum->ItemId = '<?php echo $product->getMaster() ?>';

$item->item->InventTable->ItemGroupId = '<?php echo implode(',', $categories) ?>';
$item->item->InventTable->ItemGroupName = '<?php echo $product->getMaster() ?>';
$item->item->InventTable->ItemName = '<?php echo $product->getMaster() ?>';
$item->item->InventTable->ItemType = 'Vare';

$item->item->InventTable->NetWeight = 0;
$item->item->InventTable->BlockedDate = 0;
$item->item->InventTable->WebEnabled = 1;
$item->item->InventTable->WashInstruction = '<?php echo $product->getWashing() ?>';

$item->item->InventTable->Sales->Price = 1;
$item->item->InventTable->Sales->PriceUnit = 1;
$item->item->InventTable->Sales->StandardQty = 1;
$item->item->InventTable->Sales->UnitId = '<?php echo preg_replace('/[0-9]+ /', '', $product->getUnit()) ?>';

$item->item->InventTable->WebDomain = array();
$price->priceList->SalesPrice = array();
$stock->inventoryOnHand->InventSum->InventDim = array();

<?php
            $used = array();
            foreach ($product->getProductsDomainsPricess() as $price) {
                $domain = DomainsQuery::create()->findPk($price->getDomainsId());
                if (isset($used[$domain->getDomainKey()])) {
                    continue;
                }
                $used[$domain->getDomainKey()] = TRUE;
?>
$item->item->InventTable->WebDomain[] = '<?php echo $domain->getDomainKey() ?>';
<?php
            }
?>

$item->item->InventTable->InventDim = array();
<?php
            // variants
            $a = $b = $c = $i = 0;
            foreach ($product->getProductssRelatedBySku() as $related) {
?>

$item->item->InventTable->InventDim[<?php echo $a ?>]->InventSizeId  = '<?php echo $related->getSize() ?>';
$item->item->InventTable->InventDim[<?php echo $a ?>]->InventColorId = '<?php echo $related->getColor() ?>';

<?php
                $a++;
                $map = array(
                    1 => 'DKK',
                    2 => 'NOK',
                    3 => 'SEK',
                    4 => 'EUR',
                    5 => 'EUR',
                );
                $map2 = array(
                    '1' => 'DK',
                    '2' => 'NO',
                    '3' => 'SE',
                    '4' => 'NL',
                    '5' => 'COM',
                    '6' => 'COM',
                );

                // domain prices
                foreach ($related->getProductsDomainsPricess() as $price) {
?>

$price->priceList->SalesPrice[<?php echo $b ?>]->AmountCur     = <?php echo ($price->getPrice() + $price->getVat()) ?>;
$price->priceList->SalesPrice[<?php echo $b ?>]->Currency      = '<?php echo $map[$price->getDomainsId()] ?>';
$price->priceList->SalesPrice[<?php echo $b ?>]->CustAccount   = '<?php echo $map2[$price->getDomainsId()] ?>';
$price->priceList->SalesPrice[<?php echo $b ?>]->InventSizeId  = '<?php echo $related->getSize() ?>';
$price->priceList->SalesPrice[<?php echo $b ?>]->InventColorId = '<?php echo $related->getColor() ?>';
$price->priceList->SalesPrice[<?php echo $b ?>]->PriceDate     = '<?php echo $price->getFromDate('Y-m-d') ?>';
$price->priceList->SalesPrice[<?php echo $b ?>]->PriceDateTo   = '<?php echo $price->getToDate('Y-m-d') ?>';
$price->priceList->SalesPrice[<?php echo $b ?>]->PriceUnit     = 1;
$price->priceList->SalesPrice[<?php echo $b ?>]->Quantity      = 1;
$price->priceList->SalesPrice[<?php echo $b ?>]->UnitId        = '<?php echo preg_replace('/[0-9]+ /', '', $related->getUnit()) ?>';

<?php
                    $b++;
                    $i++;
                }

                // stock
                foreach($related->getProductsStocks() as $stock) {

                    $ao = 0;
                    $ap = $stock->getQuantity();
                    $aod = '';
                    if ($stock->getAvailableFrom('Ymd') > $now) {
                        $ao = $stock->getQuantity();
                        $ap = 0;
                        $aod = $stock->getAvailableFrom('Y-m-d');
                    }
?>

$stock->inventoryOnHand->InventSum->InventDim[<?php echo $c ?>]->InventSizeId = '<?php echo $related->getSize() ?>';
$stock->inventoryOnHand->InventSum->InventDim[<?php echo $c ?>]->InventColorId = '<?php echo $related->getColor() ?>';
$stock->inventoryOnHand->InventSum->InventDim[<?php echo $c ?>]->InventQtyAvailOrdered = <?php echo $ao ?>;
$stock->inventoryOnHand->InventSum->InventDim[<?php echo $c ?>]->InventQtyAvailOrderedDate = '<?php echo $aod ?>';
$stock->inventoryOnHand->InventSum->InventDim[<?php echo $c ?>]->InventQtyAvailPhysical = <?php echo $ap ?>;
$stock->inventoryOnHand->InventSum->InventDim[<?php echo $c ?>]->InventQtyPhysicalOnhand = 0;

<?php
                    $c++;
                    $i++;
                }

                $i++;
            }
?>

try {
    $result = $client->SyncItem($item);
#    print_r($result);

    $result = $client->SyncPriceList($price);
#    print_r($result);

    $result = $client->SyncInventoryOnHand($stock);
#    print_r($result);
}
catch (\SoapFault $e) {
    print_r($e);
}

<?php
            if ($split_count == 50) {
                file_put_contents(__DIR__ . '/../../../../../tmp/products_'.$all.'.php', ob_get_clean());
                ob_start();
echo '<?php';
?>

$client = new SoapClient( "http://ph.dk/app_dev.php/soap/v1/ECommerceServices/?wsdl", array(
  'trace'      => true,
  'exceptions' => true,
));
$client->__setLocation('http://ph.dk/app_dev.php/soap/v1/ECommerceServices/');

<?php
                $split_count = 0;
            }

            $all++;
        }

        $this->output->writeln('<info>Count: '.$all.'</info>');

        $content = ob_get_clean();
        file_put_contents(__DIR__ . '/../../../../../tmp/products_last.php', $content);

    }

}
