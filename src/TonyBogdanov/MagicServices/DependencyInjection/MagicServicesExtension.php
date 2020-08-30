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

        $loader = new YamlFileLoader( $container, new FileLocator( __DIR__ . '/../../../../config' ) );
        $loader->load( 'magic_services.yaml' );

        $config = $this->processConfiguration( new MagicServicesConfiguration(), $configs );

        $container->setParameter( 'magic_services.definitions.autowire', $config['definitions']['autowire'] );
        $container->setParameter( 'magic_services.definitions.autoconfigure', $config['definitions']['autoconfigure'] );
        $container->setParameter( 'magic_services.definitions.path', $config['definitions']['path'] );
        $container->setParameter( 'magic_services.definitions.services', $config['definitions']['services'] );

        $container->setParameter( 'magic_services.aware.path', $config['aware']['path'] );
        $container->setParameter( 'magic_services.aware.namespace', $config['aware']['namespace'] );
        $container->setParameter( 'magic_services.aware.parameters', $config['aware']['parameters'] );
        $container->setParameter( 'magic_services.aware.services', $config['aware']['services'] );

    }

}
