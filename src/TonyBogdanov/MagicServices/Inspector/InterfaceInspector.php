<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Inspector;

use TonyBogdanov\MagicServices\Object\InterfaceObject;

/**
 * Class InterfaceInspector
 *
 * @package TonyBogdanov\MagicServices\Inspector
 */
class InterfaceInspector {

    /**
     * @var string
     */
    protected $awarePath;

    /**
     * @var string
     */
    protected $awareNamespace;

    /**
     * InterfaceInspector constructor.
     *
     * @param string $awarePath
     * @param string $awareNamespace
     */
    public function __construct( string $awarePath, string $awareNamespace ) {

        $this->awarePath = $awarePath;
        $this->awareNamespace = $awareNamespace;

    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getFullName( string $name ): string {

        $name = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $name ) ) );
        $name = str_replace( ' ', '', ucwords( str_replace( '.', ' ', $name ) ) );
        $name = $this->awareNamespace . '\\' . $name . '\\' . $name . 'AwareInterface';

        return $name;

    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getShortName( string $name ): ?string {

        if ( ! preg_match( '/^' . preg_quote( $this->awareNamespace, '/' ) .
            '\\\\(?P<name>[A-Z][a-zA-Z_]*?)\\\\\\1AwareInterface$/', $name, $match ) ) {

            return null;

        }

        return $match['name'];

    }

    /**
     * @param string $name
     *
     * @return InterfaceObject|null
     */
    public function createInterfaceByFullName( string $name ): ?InterfaceObject {

        $shortName = $this->getShortName( $name );
        if ( ! $shortName ) {

            return null;

        }

        return new InterfaceObject( $name, $shortName, str_replace( '\\', DIRECTORY_SEPARATOR, $this->awarePath .
            substr( $name, strlen( $this->awareNamespace ) ) ) . '.php' );

    }

    /**
     * @param string $name
     *
     * @return InterfaceObject|null
     */
    public function createInterfaceByShortName( string $name ): ?InterfaceObject {

        return $this->createInterfaceByFullName( $this->getFullName( $name ) );

    }

//    public function getInterfaces(): array {
//
//        $interfaces = [];
//
//        /** @var SplFileInfo $file */
//        foreach ( ( new Finder() )->in( $this->awarePath )->files() as $file) {
//
//            $fullName = str_replace(
//
//                DIRECTORY_SEPARATOR,
//                '\\',
//                substr( $file->getRelativePathname(), 0, -1 - strlen( $file->getExtension() ) )
//
//            );
//
//            $shortName = $this->getShortName( $fullName );
//            if ( ! $shortName ) {
//
//                continue;
//
//            }
//
//            $interfaces[ $shortName ] = new InterfaceObject( $fullName, $shortName, $file->getRealPath() );
//
//        }
//
//        return $interfaces;
//
//    }

}
