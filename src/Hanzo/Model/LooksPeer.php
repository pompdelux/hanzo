<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseLooksPeer;


/**
 * Skeleton subclass for performing query and update operations on the 'looks' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.src.Hanzo.Model
 */
class LooksPeer extends BaseLooksPeer
{
    public static function getLookProductsByLookId($look_id, $pager, $show)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');
        $domain_id = $hanzo->get('core.domain_id');

        $result = LooksQuery::create()
            ->useProductsImagesQuery()
                ->groupByImage()
            ->endUse()
            ->joinWithProductsImages()
            ->orderBySort()
            ->filterByLooksId($look_id)
            ->paginate($pager, 12)
        ;

        $records = array();
        foreach ($result as $record) {
            
            $records[] = array(
                'image' => $record->getProductsImages()->getImage(),
                'url' => $router->generate('product_set', array(
                    'image_id' => $$record->getProductsImages()->getId()
                )),
            );
        }

        $data = array(
            'title' => '',
            'products' => $records,
            'paginate' => NULL,
        );
        
        if ($result->haveToPaginate()) {

            $pages = array();
            foreach ($result->getLinks(20) as $page) {
                $pages[$page] = $router->generate($route, array('pager' => $page, 'show' => $show), TRUE);
            }

            $data['paginate'] = array(
                'next' => ($result->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getNextPage()), TRUE)),
                'prew' => ($result->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getPreviousPage()), TRUE)),

                'pages' => $pages,
                'index' => $pager
            );
        }

        return $data;
	}
}
