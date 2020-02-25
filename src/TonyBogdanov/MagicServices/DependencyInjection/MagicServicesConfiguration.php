<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class MagicServicesConfiguration
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection
 */
class MagicServicesConfiguration implements ConfigurationInterface {

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder() {

        $treeBuilder = new TreeBuilder( 'magic_services' );

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $root = $rootNode->children();

        $root
            ->scalarNode( 'aware_path' )
                ->isRequired()
                ->cannotBeEmpty()

                ->validate()
                    ->ifTrue( function ( $value ) { return ! is_string( $value ); } )
                    ->thenInvalid( 'Value must be a string.' )
                ->end()
            ->end()

            ->scalarNode( 'aware_namespace' )
                ->isRequired()
                ->cannotBeEmpty()

                ->validate()
                    ->ifTrue( function ( $value ) { return ! is_string( $value ); } )
                    ->thenInvalid( 'Value must be a string.' )
                ->end()
            ->end()

            ->scalarNode( 'config_path' )
                ->isRequired()
                ->cannotBeEmpty()

                ->validate()
                    ->ifTrue( function ( $value ) { return ! is_string( $value ); } )
                    ->thenInvalid( 'Value must be a string.' )

                    ->ifTrue( function ( string $value ) { return ! in_array( strtolower(
                        pathinfo( $value, PATHINFO_EXTENSION ) ), [ 'yml', 'yaml' ] ); } )
                    ->thenInvalid( 'Value must end with a .yml or .yaml extension.' )
                ->end()
            ->end()

            ->arrayNode( 'parameters' )
                ->defaultValue( [] )
                ->scalarPrototype( 'string' )

                    ->validate()
                        ->ifTrue( function ( $value ) { return ! is_string( $value ); } )
                        ->thenInvalid( 'Value must be a string.' )
                    ->end()
                ->end()
            ->end()

            ->arrayNode( 'interfaces' )
                ->defaultValue( [] )
                ->scalarPrototype( 'string' )

                    ->validate()
                        ->ifTrue( function ( $value ) { return ! is_string( $value ); } )
                        ->thenInvalid( 'Value must be a string.' )
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;

    }

}
