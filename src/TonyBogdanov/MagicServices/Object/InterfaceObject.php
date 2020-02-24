<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Object;

use TonyBogdanov\MagicServices\DependencyInjection\Config;

/**
 * Class InterfaceObject
 *
 * @package TonyBogdanov\MagicServices\Object
 */
class InterfaceObject {

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DependencyObject
     */
    protected $dependency;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param Config $config
     * @param DependencyObject $dependency
     * @param string $name
     *
     * @return InterfaceObject
     */
    public static function createFromName(

        Config $config,
        DependencyObject $dependency,
        string $name

    ): InterfaceObject {

        $name = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $name ) ) );
        $name = str_replace( ' ', '', ucwords( str_replace( '.', ' ', $name ) ) );

        return new static( $config, $dependency, $name );

    }

    /**
     * @param Config $config
     * @param \ReflectionClass $reflection
     *
     * @return InterfaceObject|null
     * @throws \ReflectionException
     */
    public static function createFromReflection( Config $config, \ReflectionClass $reflection ): ?InterfaceObject {

        if ( ! preg_match( '/^([A-Z][a-zA-Z0-9_]*)AwareInterface$/', $reflection->getShortName(), $match ) ) {

            throw new \RuntimeException( sprintf( 'Invalid interface name: %s.', $reflection->getShortName() ) );

        }

        $name = substr( $reflection->getShortName(), 0, -14 );
        $namespace = $config->getAwareNamespace() . '\\' . $name;

        if ( $namespace !== $reflection->getNamespaceName() ) {

            throw new \RuntimeException( sprintf(

                'Invalid interface namespace: %s.',
                $reflection->getNamespaceName()

            ) );

        }

        $getter = $reflection->getMethod( 'get' . $name );
        $setter = $reflection->getMethod( 'set' . $name );

        if ( 1 !== $setter->getNumberOfParameters() ) {

            throw new \RuntimeException( sprintf(

                'Invalid setter definition (wrong number of arguments): %s.',
                $setter->getName()

            ) );

        }

        $setterParameter = $setter->getParameters()[0];

        $getterType = $getter->getReturnType();
        $setterType = $setterParameter->getType();

        if (

            $getterType->isBuiltin() !== $setterType->isBuiltin() ||
            $getterType->getName() !== $setterType->getName()

        ) {

            throw new \RuntimeException( sprintf(

                'Invalid getter/setter definition (parameter types differ): (%1$s) %2$s / (%3$s) %4$s.',
                $getterType->getName(),
                $getter->getName(),
                $setterType->getName(),
                $setter->getName(),

            ) );

        }

        $setterName = $setterParameter->getName();

        if ( $name !== ucfirst( $setterName ) ) {

            throw new \RuntimeException( sprintf(

                'Invalid setter definition (parameter name is invalid): %s.',
                $setterName

            ) );

        }

        $dependency = DependencyObject::createFromType(

            $config,
            $name,
            $setterParameter->getType()->getName(),
            $setterParameter->getType()->isBuiltin()

        );

        return static::createFromName( $config, $dependency, $name );

    }

    /**
     * InterfaceObject constructor.
     *
     * @param Config $config
     * @param DependencyObject $dependency
     * @param string $name
     */
    public function __construct( Config $config, DependencyObject $dependency, string $name ) {

        $this
            ->setConfig( $config )
            ->setDependency( $dependency )
            ->setName( $name );

    }

    /**
     * @param InterfaceObject $interface
     *
     * @return bool
     */
    public function same( InterfaceObject $interface ): bool {

        return
            $this->getName() === $interface->getName() &&
            $this->getDependency()->same( $interface->getDependency() );

    }

    /**
     * @return Config
     */
    public function getConfig(): Config {

        return $this->config;

    }

    /**
     * @param Config $config
     *
     * @return InterfaceObject
     */
    public function setConfig( Config $config ): InterfaceObject {

        $this->config = $config;
        return $this;

    }

    /**
     * @return DependencyObject
     */
    public function getDependency(): DependencyObject {

        return $this->dependency;

    }

    /**
     * @param DependencyObject $dependency
     *
     * @return InterfaceObject
     */
    public function setDependency( DependencyObject $dependency ): InterfaceObject {

        $this->dependency = $dependency;
        return $this;

    }

    /**
     * @return string
     */
    public function getName(): string {

        return $this->name;

    }

    /**
     * @param string $name
     *
     * @return InterfaceObject
     */
    public function setName( string $name ): InterfaceObject {

        $this->name = $name;
        return $this;

    }

    /**
     * @return string
     */
    public function getNamespaceName(): string {

        return $this->getConfig()->getAwareNamespace() . '\\' . $this->getName();

    }

    /**
     * @return string
     */
    public function getClassName(): string {

        return $this->getConfig()->getAwareNamespace() . '\\' . $this->getName() . '\\' . $this->getName() .
            'AwareInterface';

    }

    /**
     * @return string
     */
    public function getBaseClassName(): string {

        return $this->getName() . 'AwareInterface';

    }

    /**
     * @return string
     */
    public function getPath(): string {

        return
            $this->getConfig()->getAwarePath() . DIRECTORY_SEPARATOR .
            $this->getName() . DIRECTORY_SEPARATOR .
            $this->getName() . 'AwareInterface.php';

    }

}
