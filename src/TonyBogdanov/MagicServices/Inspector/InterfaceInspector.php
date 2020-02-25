<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Inspector;

use TonyBogdanov\MagicServices\DependencyInjection\Config;
use TonyBogdanov\MagicServices\Object\DependencyObject;
use TonyBogdanov\MagicServices\Object\InterfaceObject;

/**
 * Class InterfaceInspector
 *
 * @package TonyBogdanov\MagicServices\Inspector
 */
class InterfaceInspector {

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param string $path
     *
     * @return string
     */
    protected function inspectFile( string $path ): string {

        $content = @file_get_contents( $path );
        if ( ! $content ) {

            throw new \RuntimeException( sprintf( 'Cannot open file for inspection: %s.', $path ) );

        }

        $tokens = token_get_all( $content );

        $namespace = '';
        $class = '';

        for ( $i = 0, $c = count( $tokens ); $i < $c; $i++ ) {

            if ( ! is_array( $tokens[ $i ] ) ) {

                continue;

            }

            if ( T_NAMESPACE === $tokens[ $i ][0] ) {

                $i++; // namespace.
                $i++; // whitespace.

                for ( ; $i < $c; $i++ ) {

                    if (

                        ! is_array( $tokens[ $i ] ) ||
                        ! in_array( $tokens[ $i ][0], [ T_STRING, T_NS_SEPARATOR ] )

                    ) {

                        break;

                    }

                    $namespace .= $tokens[ $i ][1];

                }

                continue;

            }

            if ( T_CLASS === $tokens[ $i ][0] ) {

                $i++; // class.
                $i++; // whitespace.

                for ( ; $i < $c; $i++ ) {

                    if ( ! is_array( $tokens[ $i ] ) || T_STRING !== $tokens[ $i ][0] ) {

                        break;

                    }

                    $class .= $tokens[ $i ][1];

                }

                continue;

            }

        }

        return $namespace . '\\' . $class;

    }

    /**
     * InterfaceInspector constructor.
     *
     * @param Config $config
     */
    public function __construct( Config $config ) {

        $this->config = $config;

    }

    /**
     * @return bool
     */
    public function canFindInterfaces(): bool {

        return 0 < count( $this->config->getInterfaces() );

    }

    /**
     * @return array
     */
    public function resolveInterfaces(): array {

        if ( ! $this->canFindInterfaces() ) {

            return [];

        }

        $interfaces = [];

        $positive = [];
        $negative = [];

        foreach ( $this->config->getInterfaces() as $rule ) {

            if ( class_exists( $rule ) || interface_exists( $rule ) ) {

                $dependency = DependencyObject::createFromType( $this->config, $rule, $rule, false );
                $interface = InterfaceObject::createFromName( $this->config, $dependency, $rule );

                $interfaces[ $interface->getClassName() ] = $interface;
                continue;

            }

            if ( '!' === substr( $rule, 0, 1 ) ) {

                $negative[] = substr( $rule, 1 );

            } else {

                $positive[] = $rule;

            }

        }

        if ( 0 === count( $positive ) ) {

            return $interfaces;

        }

        $include = [];
        $exclude = [];

        foreach ( $positive as $pattern ) {

            foreach ( glob( $pattern, GLOB_BRACE ) as $path ) {

                $include[] = $path;

            }

        }

        foreach ( $negative as $pattern ) {

            foreach ( glob( $pattern, GLOB_BRACE ) as $path ) {

                $exclude[] = $path;

            }

        }

        foreach ( array_values( array_diff( $include, $exclude ) ) as $path ) {

            $class = $this->inspectFile( $path );

            if ( array_key_exists( $class, $interfaces ) ) {

                continue;

            }

            $dependency = DependencyObject::createFromType( $this->config, $class, $class, false );
            $interface = InterfaceObject::createFromName( $this->config, $dependency, $class );

            $interfaces[ $interface->getClassName() ] = $interface;

        }

        return $interfaces;

    }

    /**
     * @return InterfaceObject[]
     * @throws \ReflectionException
     */
    public function resolveGeneratedInterfaces(): array {

        $interfaces = [];

        foreach ( glob( $this->config->getAwarePath() . '/*/*AwareInterface.php' ) as $path ) {

            $class =
                $this->config->getAwareNamespace() . '\\' .
                pathinfo( dirname( $path ), PATHINFO_BASENAME ) . '\\' .
                pathinfo( $path, PATHINFO_FILENAME );

            $interfaces[ $class ] = InterfaceObject::createFromReflection(

                $this->config,
                new \ReflectionClass( $class )

            );

        }

        return $interfaces;

    }

}
