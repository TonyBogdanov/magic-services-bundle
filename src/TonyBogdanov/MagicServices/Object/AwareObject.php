<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Object;

/**
 * Class AwareObject
 *
 * @package TonyBogdanov\MagicServices\Object
 */
class AwareObject {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $dependency;

    /**
     * AwareObject constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $dependency
     */
    public function __construct( string $name, string $type, string $dependency ) {

        $this
            ->setName( $name )
            ->setType( $type )
            ->setDependency( $dependency );

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
     * @return AwareObject
     */
    public function setName( string $name ): AwareObject {

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
     * @return AwareObject
     */
    public function setType( string $type ): AwareObject {

        $this->type = $type;
        return $this;

    }

    /**
     * @return string
     */
    public function getDependency(): string {

        return $this->dependency;

    }

    /**
     * @param string $dependency
     *
     * @return AwareObject
     */
    public function setDependency( string $dependency ): AwareObject {

        $this->dependency = $dependency;
        return $this;

    }

}
