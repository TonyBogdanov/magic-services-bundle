<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class MagicServicesExtension
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection
 */
class MagicServicesExtension extends Extension {

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load( array $configs, ContainerBuilder $container ) {

        $config = $this->processConfiguration( new MagicServicesConfiguration(), $configs );

        $loader = new YamlFileLoader( $container, new FileLocator( __DIR__ . '/../../../../config' ) );
        $loader->load( 'services.yaml' );

        $container->setParameter(

            'magic_services.definitions.path',
            isset( $config['definitions'] ) ? ( $config['definitions']['path'] ?? null ) : null

        );

        $container->setParameter(

            'magic_services.aware.path',
            isset( $config['aware'] ) ? ( $config['aware']['path'] ?? null ) : null

        );

        $container->setParameter(

            'magic_services.aware.namespace',
            isset( $config['aware'] ) ? ( $config['aware']['namespace'] ?? null ) : null

        );

        $container->setParameter(

            'magic_services.aware.parameters',
            isset( $config['aware'] ) ? ( $config['aware']['parameters'] ?? [] ) : []

        );

        $container->setParameter(

            'magic_services.aware.services',
            isset( $config['aware'] ) ? ( $config['aware']['services'] ?? [] ) : []

        );

    }

}
