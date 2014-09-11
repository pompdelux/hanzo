<?php

namespace Hanzo\Bundle\PaymentBundle;

use Hanzo\Bundle\PaymentBundle\DependencyInjection\Compiler\AggregatedTaggedServicesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PaymentBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AggregatedTaggedServicesPass());
    }
}
