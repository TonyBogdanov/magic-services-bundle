<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Object;

use TonyBogdanov\MagicServices\DependencyInjection\Config;
use TonyBogdanov\MagicServices\Util\TypeUtil;

/**
 * Class DependencyObject
 *
 * @package TonyBogdanov\MagicServices\Object
 */
class DependencyObject {

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $primitive;

    /**
     * @param Config $config
     * @param string $name
     * @param $value
     *
     * @return DependencyObject
     */
    public static function createFromValue( Config $config, string $name, $value ): DependencyObject {

        $primitive = ! is_object( $value );

        return static::createFromType(

            $config,
            $name,
            $primitive ? gettype( $value ) : get_class( $value ),
            $primitive

        );

    }

    /**
     * @param Config $config
     * @param string $name
     * @param string $type
     * @param bool $primitive
     *
     * @return DependencyObject
     */
    public static function createFromType(

        Config $config,
        string $name,
        string $type,
        bool $primitive

    ): DependencyObject {

        $name = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $name ) ) );
        $name = str_replace( ' ', '', ucwords( str_replace( '.', ' ', $name ) ) );
        $name = lcfirst( $name );

        return new static( $config, $name, $type, $primitive );

    }

    /**
     * DependencyObject constructor.
     *
     * @param Config $config
     * @param string $name
     * @param string $type
     * @param bool $primitive
     */
    public function __construct( Config $config, string $name, string $type, bool $primitive ) {

        $this
            ->setConfig( $config )
            ->setName( $name )
            ->setType( $type )
            ->setPrimitive( $primitive );

    }

    /**
     * @param DependencyObject $dependency
     *
     * @return bool
     */
    public function same( DependencyObject $dependency ): bool {

        return
            $this->getName() === $dependency->getName() &&
            TypeUtil::normalize( $this->getType() ) === TypeUtil::normalize( $dependency->getType() ) &&
            $this->isPrimitive() === $dependency->isPrimitive();

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
     * @return DependencyObject
     */
    public function setConfig( Config $config ): DependencyObject {

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
     * @return DependencyObject
     */
    public function setName( string $name ): DependencyObject {

        $this->name = $name;
        return $this;

    }

    /**
     * @return string
     */
    public function getType(): string {

        return $this->type;

    }

    /**
     * @param string $type
     *
     * @return DependencyObject
     */
    public function setType( string $type ): DependencyObject {

        $this->type = $type;
        return $this;

    }

    /**
     * @return bool
     */
    public function isPrimitive(): bool {

        return $this->primitive;

    }

    /**
     * @param bool $primitive
     *
     * @return DependencyObject
     */
    public function setPrimitive( bool $primitive ): DependencyObject {

        $this->primitive = $primitive;
        return $this;

    }

}
