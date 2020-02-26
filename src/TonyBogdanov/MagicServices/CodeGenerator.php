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
use TonyBogdanov\MagicServices\Util\Normalizer;

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
                ->addProperty( Normalizer::normalizeParameterName( $name ) )
                ->addComment( '@var ' . $baseType . ' $' . Normalizer::normalizeParameterName( $name ) )
                ->setProtected();

        }

        $getter = $class
            ->addMethod( 'get' . $name )
            ->addComment( '@return ' . $baseType )
            ->setPublic()
            ->setReturnType( $type );

        if ( ! $interface ) {

            $getter->setBody( 'return $this->' . Normalizer::normalizeParameterName( $name ) . ';' );

        }

        $setter = $class
            ->addMethod( 'set' . $name )
            ->addComment( '@param ' . $baseType . ' $' . Normalizer::normalizeParameterName( $name ) )
            ->addComment( "\n" . '@return $this' )
            ->setPublic();

        $setter->addParameter( Normalizer::normalizeParameterName( $name ) )->setType( $type );

        if ( ! $interface ) {

            $setter->setBody(

                '$this->' . Normalizer::normalizeParameterName( $name ) . ' = $' .
                Normalizer::normalizeParameterName( $name ) . ';' . "\n" .
                'return $this;'

            );

        }

        return $file;

    }

}
