<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Object;

use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * Class AwareObject
 *
 * @package TonyBogdanov\MagicServices\Object
 */
class AwareObject {

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string|object
     */
    protected $dependency;

    /**
     * AwareObject constructor.
     *
     * @param string $name
     * @param string $type
     * @param string|object $dependency
     */
    public function __construct( string $name, string $type, $dependency ) {

        $this
            ->setName( $name )
            ->setType( $type )
            ->setDependency( $dependency );

    }

    /**
     * @return string
     */
    public function __toString(): string {

        $dependency = $this->getDependency();

        return implode( ',', [

            $this->getName(),
            $this->getType(),
            $dependency instanceof TaggedValue ?
                '!' . $dependency->getTag() . ' ' . $dependency->getValue() :
                $dependency

        ] );

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
     * @return object|string
     */
    public function getDependency() {

        return $this->dependency;

    }

    /**
     * @param object|string $dependency
     *
     * @return $this
     */
    public function setDependency( $dependency ) {

        $this->dependency = $dependency;
        return $this;

    }

}
