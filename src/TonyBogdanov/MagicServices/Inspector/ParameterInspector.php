<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Inspector;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Config;
use TonyBogdanov\MagicServices\Object\ParameterObject;

/**
 * Class ParameterInspector
 *
 * @package TonyBogdanov\MagicServices\Inspector
 */
class ParameterInspector {

    /**
     * @var ParameterBagInterface
     */
    protected $bag;

    /**
     * @var Config
     */
    protected $config;

    /**
     * ParameterInspector constructor.
     *
     * @param ParameterBagInterface $bag
     * @param Config $config
     */
    public function __construct( ParameterBagInterface $bag, Config $config ) {

        $this->bag = $bag;
        $this->config = $config;

    }

    /**
     * @return bool
     */
    public function hasRegex(): bool {

        return ! is_null( $this->config->getParametersRegex() );

    }

    /**
     * @return ParameterObject[]
     */
    public function getParameters(): array {

        if ( ! $this->hasRegex() ) {

            return [];

        }

        $parameters = [];

        foreach ( $this->bag->all() as $name => $value ) {

            if ( ! preg_match( $this->config->getParametersRegex(), $name ) ) {

                continue;

            }

            $parameter = ParameterObject::create( $this->config, $name, $value );
            if ( ! $parameter ) {

                continue;

            }

            $parameters[ $name ] = $parameter;

        }

        return $parameters;

    }

}
