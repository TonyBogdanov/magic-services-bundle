<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices;

use Nette\PhpGenerator\PhpFile;
use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;

/**
 * Class CodeGenerator
 *
 * @package TonyBogdanov\MagicServices
 */
class CodeGenerator {

    /**
     * @param bool $interface
     * @param string $className
     * @param string $name
     * @param string $type
     *
     * @return PhpFile
     */
    public function generate( bool $interface, string $className, string $name, string $type ): PhpFile {

        $parts = explode( '\\', $className );

        $baseClassName = array_pop( $parts );
        $namespaceName = implode( '\\', $parts );

        $parts = explode( '\\', $type );
        $baseType = array_pop( $parts );

        $primitive = ! class_exists( $type ) && ! interface_exists( $type );

        $file = new PhpFile();
        $namespace = $file->addNamespace( $namespaceName );

        if ( $interface ) {

            $namespace->addUse( ServiceAwareInterface::class );

        }

        if ( ! $primitive ) {

            $namespace->addUse( $type );

        }

        $class = ( $interface ? $namespace->addInterface( $baseClassName ) : $namespace->addTrait( $baseClassName ) )
            ->addComment( 'This file was automatically generated by the tonybogdanov/magic-services-bundle package.' )
            ->addComment( 'Do not manually modify this file.' )
            ->addComment( "\n" . ( $interface ? 'Interface' : 'Trait' ) . ' ' . $baseClassName )
            ->addComment( "\n" . '@package ' . $namespaceName );

        if ( $interface ) {

            $class->setExtends( ServiceAwareInterface::class );

        } else {

            $class
                ->addProperty( lcfirst( $name ) )
                ->addComment( '@var ' . $baseType . ' $' . lcfirst( $name ) )
                ->setProtected();

        }

        $getter = $class
            ->addMethod( 'get' . $name )
            ->addComment( '@return ' . $baseType )
            ->setPublic()
            ->setReturnType( $type );

        if ( ! $interface ) {

            $getter->setBody( 'return $this->' . lcfirst( $name ) . ';' );

        }

        $setter = $class
            ->addMethod( 'set' . $name )
            ->addComment( '@param ' . $baseType . ' $' . lcfirst( $name ) )
            ->addComment( "\n" . '@return $this' )
            ->setPublic();

        $setter->addParameter( lcfirst( $name ) )->setType( $type );

        if ( ! $interface ) {

            $setter->setBody(

                '$this->' . lcfirst( $name ) . ' = $' . lcfirst( $name ) . ';' . "\n" .
                'return $this;'

            );

        }

        return $file;

    }

}
