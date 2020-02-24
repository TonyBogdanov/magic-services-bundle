<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Util;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Filesystem\Filesystem;
use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;
use TonyBogdanov\MagicServices\Object\DependencyObject;
use TonyBogdanov\MagicServices\Object\InterfaceObject;

/**
 * Class Generator
 *
 * @package TonyBogdanov\MagicServices\Util
 */
class Generator {

    /**
     * @param DependencyObject $dependency
     * @param PhpNamespace $namespace
     * @param Method $method
     * @param bool $withBody
     *
     * @return Method
     */
    protected function dumpGetter(

        DependencyObject $dependency,
        PhpNamespace $namespace,
        Method $method,
        bool $withBody

    ): Method {

        if ( ! $dependency->isPrimitive() ) {

            $namespace->addUse( $dependency->getType() );

        }

        $method->setComment( "@return " . TypeUtil::normalize( $dependency->getType() ) );

        $method->setPublic();
        $method->setReturnType( TypeUtil::normalize( $dependency->getType() ) );

        if ( $withBody ) {

            $method->setBody( 'return $this->' . $dependency->getName() . ';' );

        }

        return $method;

    }

    /**
     * @param DependencyObject $dependency
     * @param PhpNamespace $namespace
     * @param Method $method
     * @param bool $withBody
     *
     * @return Method
     */
    protected function dumpSetter(

        DependencyObject $dependency,
        PhpNamespace $namespace,
        Method $method,
        bool $withBody

    ): Method {

        if ( ! $dependency->isPrimitive() ) {

            $namespace->addUse( $dependency->getType() );

        }

        $method->setComment(

            "@param " . TypeUtil::normalize( $dependency->getType() ) . " \$" . $dependency->getName() . "\n\n" .
            "@return \$this;"

        );

        $method->setPublic();
        $method->addParameter( $dependency->getName() )->setType( TypeUtil::normalize( $dependency->getType() ) );

        if ( $withBody ) {

            $method->setBody(

                '$this->' . $dependency->getName() . ' = $' . $dependency->getName() . ";\n" .
                'return $this;'

            );

        }

        return $method;

    }

    /**
     * @param InterfaceObject $interface
     * @param PhpNamespace $namespace
     * @param ClassType $class
     *
     * @return $this
     */
    protected function dumpInterfaceClass( InterfaceObject $interface, PhpNamespace $namespace, ClassType $class ) {

        $namespace->addUse( ServiceAwareInterface::class );

        $class->setComment(

            "Interface " . $interface->getBaseClassName() . "\n\n" .
            "@package " . $interface->getNamespaceName()

        );

        $class->setInterface();
        $class->setExtends( ServiceAwareInterface::class );

        $this->dumpGetter(

            $interface->getDependency(),
            $namespace,
            $class->addMethod( 'get' . ucfirst( $interface->getDependency()->getName() ) ),
            false

        );

        $this->dumpSetter(

            $interface->getDependency(),
            $namespace,
            $class->addMethod( 'set' . ucfirst( $interface->getDependency()->getName() ) ),
            false

        );

        return $this;

    }

    /**
     * @param InterfaceObject $interface
     *
     * @return $this
     */
    public function dumpInterface( InterfaceObject $interface ) {

        $file = new PhpFile();
        $namespace = $file->addNamespace( $interface->getNamespaceName() );

        $this->dumpInterfaceClass( $interface, $namespace, $namespace->addClass( $interface->getBaseClassName() ) );

        ( new Filesystem() )->dumpFile( $interface->getPath(), ( new PsrPrinter() )->printFile( $file ) );
        return $this;

    }

}
