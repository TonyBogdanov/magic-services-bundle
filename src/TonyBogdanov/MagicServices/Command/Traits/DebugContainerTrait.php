<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Traits;

use Closure;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use TonyBogdanov\Memoize\Traits\MemoizeTrait;

/**
 * Trait DebugContainerTrait
 *
 * @package TonyBogdanov\MagicServices\Command\Traits
 */
trait DebugContainerTrait {

    use MemoizeTrait;

    /**
     * @return ContainerBuilder
     */
    protected function getContainerBuilder(): ContainerBuilder {

        return $this->memoize( __METHOD__, function (): ContainerBuilder {

            $kernel = $this->getApplication()->getKernel();

            if (

                ! $kernel->isDebug() ||
                ! ( new ConfigCache( $kernel->getContainer()->getParameter( 'debug.container.dump' ), true ) )
                    ->isFresh()

            ) {

                $buildContainer = Closure::bind( function () {

                    return $this->buildContainer();

                }, $kernel, get_class( $kernel ) );

                $container = $buildContainer();
                $compilerPassConfig = $container->getCompilerPassConfig();

                $compilerPassConfig->setRemovingPasses( [] );
                $compilerPassConfig->setAfterRemovingPasses( [] );

                $container->compile();

            } else {

                $container = new ContainerBuilder();

                ( new XmlFileLoader( $container, new FileLocator() ) )
                    ->load( $kernel->getContainer()->getParameter( 'debug.container.dump' ) );

                $locatorPass = new ServiceLocatorTagPass();
                $locatorPass->process( $container );

            }

            return $container;

        } );

    }

}
