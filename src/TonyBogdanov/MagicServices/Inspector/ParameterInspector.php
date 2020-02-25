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
    public function canFindParameters(): bool {

        return 0 < count( $this->config->getParameters() );

    }

    /**
     * @return ParameterObject[]
     */
    public function resolveParameters(): array {

        if ( ! $this->canFindParameters() ) {

            return [];

        }

        $parameters = [];

        foreach ( $this->bag->all() as $name => $value ) {

            $found = false;

            foreach ( $this->config->getParameters() as $regex ) {

                if ( preg_match( $regex, $name ) ) {

                    $found = true;
                    break;

                }

            }

            if ( ! $found ) {

                continue;

            }

            $parameter = ParameterObject::create( $this->config, $name, $value );
            if ( ! $parameter ) {

                continue;

            }

            $parameters[] = $parameter;

        }

        return $parameters;

    }

}
