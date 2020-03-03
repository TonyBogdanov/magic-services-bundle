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

        $stringValidator = function ( $value ) { return ! is_string( $value ); };
        $stringError = 'Value must be a string.';

        $root
            ->arrayNode( 'definitions' )
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode( 'path' )
                        ->defaultValue( '%kernel.project_dir%/config/magic_services.yaml' )
                        ->validate()
                            ->ifTrue( $stringValidator )
                            ->thenInvalid( $stringError )
                        ->end()
                    ->end()
                    ->arrayNode( 'services' )
                        ->defaultValue( [ '%kernel.project_dir%/src' ] )
                        ->scalarPrototype()
                    ->end()
                ->end()
            ->end();

        $root
            ->arrayNode( 'aware' )
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode( 'path' )
                        ->defaultValue( '%kernel.project_dir%/src/DependencyInjection/Aware' )
                        ->validate()
                            ->ifTrue( $stringValidator )
                            ->thenInvalid( $stringError )
                        ->end()
                    ->end()
                    ->scalarNode( 'namespace' )
                        ->defaultValue( 'App\DependencyInjection\Aware' )
                        ->validate()
                            ->ifTrue( $stringValidator )
                            ->thenInvalid( $stringError )
                        ->end()
                    ->end()
                    ->arrayNode( 'parameters' )
                        ->defaultValue( [] )
                        ->arrayPrototype()
                            ->beforeNormalization()
                                ->ifString()
                                ->then( function ( string $value ) { return [ 'regex' => $value ]; } )
                            ->end()
                            ->children()
                                ->scalarNode( 'regex' )
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->validate()
                                        ->ifTrue( $stringValidator )
                                        ->thenInvalid( $stringError )
                                    ->end()
                                ->end()
                                ->scalarNode( 'name' )
                                    ->defaultNull()
                                    ->validate()
                                        ->ifTrue( $stringValidator )
                                        ->thenInvalid( $stringError )
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode( 'services' )
                        ->defaultValue( [] )
                        ->arrayPrototype()
                            ->beforeNormalization()
                                ->ifString()
                                ->then( function ( string $value ) { return [ 'type' => $value ]; } )
                            ->end()
                            ->children()
                                ->scalarNode( 'type' )
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->validate()
                                        ->ifTrue( $stringValidator )
                                        ->thenInvalid( $stringError )
                                    ->end()
                                ->end()
                                ->scalarNode( 'service' )
                                    ->defaultNull()
                                    ->validate()
                                        ->ifTrue( $stringValidator )
                                        ->thenInvalid( $stringError )
                                    ->end()
                                ->end()
                                ->scalarNode( 'name' )
                                    ->defaultNull()
                                    ->validate()
                                        ->ifTrue( $stringValidator )
                                        ->thenInvalid( $stringError )
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;

    }

}
