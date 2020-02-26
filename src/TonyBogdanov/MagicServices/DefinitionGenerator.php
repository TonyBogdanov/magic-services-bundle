<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices;

use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;
use TonyBogdanov\MagicServices\Object\AwareObject;
use TonyBogdanov\MagicServices\Object\DefinitionObject;
use TonyBogdanov\MagicServices\Util\Normalizer;
use TonyBogdanov\Memoize\Traits\MemoizeTrait;

/**
 * Class DefinitionGenerator
 *
 * @package TonyBogdanov\MagicServices
 */
class DefinitionGenerator {

    use MemoizeTrait;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Inspector
     */
    protected $inspector;

    /**
     * @var AwareGenerator
     */
    protected $awareGenerator;

    /**
     * @param string $interfaceName
     * @param bool $root
     *
     * @return string[]
     * @throws \ReflectionException
     */
    protected function getParentAwareInterfaceNames( string $interfaceName, bool $root = true ): array {

        $result = $root ? [] : [ $interfaceName ];
        $reflection = new \ReflectionClass( $interfaceName );

        return array_unique( array_merge( $result, ...array_map( function ( string $interfaceName ): array {

            return array_filter(

                $this->getParentAwareInterfaceNames( $interfaceName, false ),
                function ( string $interfaceName ): bool {

                    return
                        $interfaceName !== ServiceAwareInterface::class &&
                        is_a( $interfaceName, ServiceAwareInterface::class, true );

                }

            );

        }, $reflection->getInterfaceNames() ) ) );

    }

    /**
     * @return AwareObject[]
     */
    protected function getAwareObjects(): array {

        return $this->memoize( __METHOD__, function (): array {

            return array_merge(

                $this->inspector->resolveAwareParameters(),
                $this->inspector->resolveAwareServices()

            );

        } );

    }

    /**
     * @param array $awareObjects
     * @param array $awareInterfaceNames
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function filterAwareObjectsByInterfaceNames( array $awareObjects, array $awareInterfaceNames ): array {

        $result = [];

        $awareItems = array_map( function ( AwareObject $object ): array {

            return [

                'object' => $object,
                'interface' => $this->awareGenerator->getInterfaceClassName( $object ),

            ];

        }, $awareObjects );

        foreach ( $awareInterfaceNames as $index => $awareInterfaceName ) {

            foreach ( $awareItems as $awareItem ) {

                if ( $awareItem['interface'] === $awareInterfaceName ) {

                    $result[] = $awareItem['object'];
                    continue 2;

                }

                $parentAwareInterfaceNames = $this->getParentAwareInterfaceNames( $awareInterfaceName );
                if ( 0 < count( $parentAwareInterfaceNames ) ) {

                    if ( 1 < count( $parentAwareInterfaceNames ) ) {

                        throw new \RuntimeException( sprintf(

                            'Interface %1$s extends from more than one aware interface which is currently not' .
                            ' supported.',
                            $awareInterfaceName

                        ) );

                    }

                    $awareInterfaceNames[ $index-- ] = $parentAwareInterfaceNames[0];
                    continue 2;

                }

            }

            throw new \RuntimeException( sprintf(

                'Interface %1$s is considered aware, but it isn\'t matched / generated by the current configuration.',
                $awareInterfaceName

            ) );

        }

        return $result;

    }

    /**
     * @param AwareObject[] $awareObjects
     * @param array[] $dependencies
     *
     * @return AwareObject[]
     */
    protected function filterAwareObjectsByDependencies( array $awareObjects, array $dependencies ): array {

        $result = [];

        foreach ( $dependencies as $dependencyIndex => $dependency ) {

            foreach ( $awareObjects as $awareIndex => $awareObject ) {

                if (

                    $dependency['name'] === Normalizer::normalizeParameterName( $awareObject->getName() ) &&
                    $dependency['type'] === $awareObject->getType()

                ) {

                    $result[] = $awareObjects[ $awareIndex ];
                    array_splice( $awareObjects, $awareIndex, 1 );

                    continue 2;

                }

            }

            throw new \RuntimeException( sprintf(

                'Constructor parameter #%1$d (%2$s $%3$s) cannot be auto-wired because it does not have a' .
                ' corresponding aware interface.',
                $dependencyIndex + 1,
                $dependency['type'],
                $dependency['name']

            ) );

        }

        return $result;

    }

    /**
     * DefinitionGenerator constructor.
     *
     * @param Inspector $inspector
     * @param AwareGenerator $awareGenerator
     * @param string $path
     */
    public function __construct( Inspector $inspector, AwareGenerator $awareGenerator, string $path ) {

        $this->inspector = $inspector;
        $this->awareGenerator = $awareGenerator;

        $this->path = $path;

    }

    /**
     * @param DefinitionObject $object
     *
     * @return array
     * @throws \ReflectionException
     */
    public function generate( DefinitionObject $object ): array {

        try {

            $awareObjects = $this->filterAwareObjectsByInterfaceNames(

                $this->getAwareObjects(),
                $object->getAwareInterfaceNames()

            );

            $argumentObjects = $this->filterAwareObjectsByDependencies(

                $awareObjects,
                $object->getConstructorDependencies()

            );

            $setterObjects = array_values( array_diff(  $awareObjects, $argumentObjects ) );

            if ( 0 < count( $setterObjects ) && ! $object->supportsSetters() ) {

                throw new \RuntimeException( sprintf(

                    '%1$s has a dependency (%2$s $%3$s), but the class does not support magic setters. Add the' .
                    ' dependency to the constructor.',
                    $object->getName(),
                    $setterObjects[0]->getType(),
                    Normalizer::normalizeParameterName( $setterObjects[0]->getName() )

                ) );

            }

            $definition = [

                'class' => $object->getName(),
                'tags' => $object->getTags(),
                'arguments' => array_map( function ( AwareObject $object ): string {

                    return $object->getDependency();

                }, $argumentObjects ),
                'calls' => array_map( function ( AwareObject $object ): array {

                    return [

                        'set' . $object->getName(),
                        [ $object->getDependency() ],

                    ];

                }, $setterObjects ),
                'public' => $object->isPublic(),

            ];

            if ( 0 === count( $definition['tags'] ) ) {

                unset( $definition['tags'] );

            }

            if ( 0 === count( $definition['arguments'] ) ) {

                unset( $definition['arguments'] );

            }

            if ( 0 === count( $definition['calls'] ) ) {

                unset( $definition['calls'] );

            }

            if ( ! $definition['public'] ) {

                unset( $definition['public'] );

            }

            return $definition;

        } catch ( \RuntimeException $e ) {

            throw new \RuntimeException( sprintf(

                'Invalid service class: %1$s. %2$s.',
                $object->getName(),
                rtrim( $e->getMessage(), '.' )

            ) );

        }

    }

}
