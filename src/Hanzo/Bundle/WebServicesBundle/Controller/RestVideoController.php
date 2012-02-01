<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\ProductsQuery,
    Hanzo\Model\Products,
    Hanzo\Model\ProductsDomainsPricesQuery
;
use Hanzo\Core\CoreController;

class RestVideoController extends CoreController
{
    public function getAction() {}
}
