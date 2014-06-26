<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * all overrides and extensions have been moved to this trait to ease upgrades between SF versions.
 */
require __DIR__.'/KernelTrait.php';

class AppKernel extends Kernel
{
    use KernelTrait;

    /**
     * The content of this method is also moved to the trait for easier migrations.
     *
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface[]
     */
    public function registerBundles()
    {
        return $this->bundles();
    }


    /**
     * The content of this method is also moved to the trait for easier migrations.
     *
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $this->registerLoaders($loader);
    }
}

