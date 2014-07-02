<?php

namespace Hanzo\Bundle\ShippingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Hanzo\Bundle\ShippingBundle\DependencyInjection\Compiler\ValidatorPass;

class ShippingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ValidatorPass());
    }
}
