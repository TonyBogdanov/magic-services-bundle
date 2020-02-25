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
 * Class TraitObject
 *
 * @package TonyBogdanov\MagicServices\Object
 */
class TraitObject {

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
     * @return TraitObject
     */
    public static function createFromName( Config $config, DependencyObject $dependency, string $name ): TraitObject {

        $parts = explode( '\\', $name );

        if ( 0 < count( $parts ) ) {

            $name = array_pop( $parts );
            $name = preg_replace( '/Interface$/', '', $name );

        }

        $name = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $name ) ) );
        $name = str_replace( ' ', '', ucwords( str_replace( '.', ' ', $name ) ) );

        return new static( $config, $dependency, $name );

    }

    /**
     * TraitObject constructor.
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
     * @return Config
     */
    public function getConfig(): Config {

        return $this->config;

    }

    /**
     * @param Config $config
     *
     * @return TraitObject
     */
    public function setConfig( Config $config ): TraitObject {

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
     * @return TraitObject
     */
    public function setDependency( DependencyObject $dependency ): TraitObject {

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
     * @return TraitObject
     */
    public function setName( string $name ): TraitObject {

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
            'AwareTrait';

    }

    /**
     * @return string
     */
    public function getBaseClassName(): string {

        return $this->getName() . 'AwareTrait';

    }

    /**
     * @return string
     */
    public function getPath(): string {

        return
            $this->getConfig()->getAwarePath() . DIRECTORY_SEPARATOR .
            $this->getName() . DIRECTORY_SEPARATOR .
            $this->getName() . 'AwareTrait.php';

    }

}
