<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Console;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use TonyBogdanov\MagicServices\MagicServicesBundle;

/**
 * Class Kernel
 *
 * @package TonyBogdanov\MagicServices\Console
 *
 * @method string getProjectDir()
 */
class Kernel extends BaseKernel {

    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * @return iterable|BundleInterface[]
     */
    public function registerBundles() {

        return [

            new FrameworkBundle(),
            new MagicServicesBundle(),

        ];

    }

    /**
     * @param RouteCollectionBuilder $routes
     */
    protected function configureRoutes( RouteCollectionBuilder $routes ) {

    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     *
     * @throws \Exception
     */
    protected function configureContainer( ContainerBuilder $container, LoaderInterface $loader ) {

        $confDir = $this->getProjectDir() . '/config';
        $loader->load( $confDir . '/{services}' . self::CONFIG_EXTS, 'glob' );

    }

}
