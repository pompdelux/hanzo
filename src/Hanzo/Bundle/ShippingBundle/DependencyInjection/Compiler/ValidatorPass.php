<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\ShippingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ValidatorPass  implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $file = realpath(__DIR__.'/../../Resources/config/validation.'.$container->getParameterBag()->get('locale').'.yml');

        if (file_exists($file)) {
            $validatorBuilder = $container->getDefinition('validator.builder');
            $validatorFiles = [$file];
            $validatorBuilder->addMethodCall('addYamlMappings', array($validatorFiles));
        }
    }
}
