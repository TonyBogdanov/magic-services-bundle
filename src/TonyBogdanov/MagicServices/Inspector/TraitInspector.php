<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Inspector;

use TonyBogdanov\MagicServices\DependencyInjection\Config;
use TonyBogdanov\MagicServices\Object\InterfaceObject;
use TonyBogdanov\MagicServices\Object\TraitObject;

/**
 * Class TraitInspector
 *
 * @package TonyBogdanov\MagicServices\Inspector
 */
class TraitInspector {

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var InterfaceInspector
     */
    protected $interfaceInspector;

    /**
     * TraitInspector constructor.
     *
     * @param Config $config
     * @param InterfaceInspector $interfaceInspector
     */
    public function __construct( Config $config, InterfaceInspector $interfaceInspector ) {

        $this->config = $config;
        $this->interfaceInspector = $interfaceInspector;

    }

    /**
     * @return TraitObject[]
     * @throws \ReflectionException
     */
    public function resolveTraits(): array {

        return array_map( function ( InterfaceObject $interface ): TraitObject {

            return $interface->getTrait();

        }, $this->interfaceInspector->resolveGeneratedInterfaces() );

    }

}
