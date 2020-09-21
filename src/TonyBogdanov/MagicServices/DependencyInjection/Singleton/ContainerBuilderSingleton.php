<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\DependencyInjection\Singleton;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\BuildDebugContainerTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Trait ContainerBuilderTrait
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Traits
 */
trait ContainerBuilderSingleton {

    /**
     * @var ContainerBuilder
     */
    protected static ContainerBuilder $containerBuilder;

    /**
     * @return ContainerBuilder
     */
    public static function getContainerBuilder(): ContainerBuilder {

        if ( ! isset( self::$containerBuilder ) ) {

            throw new RuntimeException( sprintf(

                'Cannot resolve dependency on %1$s because it was never set in the singleton. To use this' .
                ' functionality you must explicitly call %2$s::setContainerBuilder and pass a reference to %1$s.' .
                ' One can be obtained through the internal %3$s from within a console command.',

                ContainerBuilder::class,
                __CLASS__,
                BuildDebugContainerTrait::class

            ) );

        }

        return self::$containerBuilder;

    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    public static function setContainerBuilder( ContainerBuilder $containerBuilder ): void {

        self::$containerBuilder = $containerBuilder;

    }

}
