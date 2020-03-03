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

    protected const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * @var string
     */
    protected $configPath;

    /**
     * Kernel constructor.
     *
     * @param string $configPath
     * @param string $environment
     * @param bool $debug
     */
    public function __construct( string $configPath, string $environment, bool $debug ) {

        parent::__construct( $environment, $debug );

        $this->configPath = $configPath;

    }

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
        
        $loader->load( $this->configPath );

    }

}
