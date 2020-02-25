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
 * Class ParameterObject
 *
 * @package TonyBogdanov\MagicServices\Object
 */
class ParameterObject {

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var DependencyObject
     */
    protected $dependency;

    /**
     * @var InterfaceObject
     */
    protected $interface;

    /**
     * @param Config $config
     * @param string $name
     * @param $value
     *
     * @return ParameterObject
     */
    public static function create( Config $config, string $name, $value ): ParameterObject {

        return new static( $config, $name, $value );

    }

    /**
     * ParameterObject constructor.
     *
     * @param Config $config
     * @param string $name
     * @param $value
     */
    public function __construct( Config $config, string $name, $value ) {

        $this
            ->setConfig( $config )
            ->setName( $name )
            ->setValue( $value );

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
     * @return ParameterObject
     */
    public function setConfig( Config $config ): ParameterObject {

        $this->config = $config;
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
     * @return ParameterObject
     */
    public function setName( string $name ): ParameterObject {

        $this->name = $name;
        return $this;

    }

    /**
     * @return mixed
     */
    public function getValue() {

        return $this->value;

    }

    /**
     * @param mixed $value
     *
     * @return ParameterObject
     */
    public function setValue( $value ) {

        $this->value = $value;
        return $this;

    }

    /**
     * @return DependencyObject
     */
    public function getDependency(): DependencyObject {

        if ( ! isset( $this->dependency ) ) {

            $this->dependency = DependencyObject::createFromValue(

                $this->getConfig(),
                'parameter' . ucfirst( $this->getName() ),
                $this->getValue()

            );

        }

        return $this->dependency;

    }

    /**
     * @return InterfaceObject
     */
    public function getInterface(): InterfaceObject {

        if ( ! isset( $this->interface ) ) {

            $this->interface = InterfaceObject::createFromName(

                $this->getConfig(),
                $this->getDependency(),
                'parameter' . ucfirst( $this->getName() )

            );

        }

        return $this->interface;

    }

}
