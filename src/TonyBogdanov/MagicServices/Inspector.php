<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices;

use Nette\PhpGenerator\Type;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TonyBogdanov\MagicServices\Object\AwareObject;
use TonyBogdanov\MagicServices\Util\Normalizer;

/**
 * Class Inspector
 *
 * @package TonyBogdanov\MagicServices
 */
class Inspector {

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var string[]
     */
    protected $parameters;

    /**
     * @var string[]
     */
    protected $services;

    /**
     * Inspector constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param array $parameters
     * @param array $services
     */
    public function __construct( ParameterBagInterface $parameterBag, array $parameters, array $services ) {

        $this->parameterBag = $parameterBag;

        $this->parameters = $parameters;
        $this->services = $services;

    }

    /**
     * @return AwareObject[]
     */
    public function resolveParameters(): array {

        $objects = [];

        foreach ( $this->parameters as $parameter ) {

            $matchRegex = $parameter['regex'];
            $matchName = $parameter['name'] ?? 'Parameter$0';

            foreach ( $this->parameterBag->all() as $name => $value ) {

                if ( ! preg_match( $matchRegex, $name, $matches ) ) {

                    continue;

                }

                $currentName = $matchName;

                for ( $i = count( $matches ) - 1; 0 <= $i; $i-- ) {

                    $currentName = preg_replace(

                        '/(?:\\$|\\\\\\\)' . $i . '/',
                        Normalizer::normalizeName( $matches[ $i ] ),
                        $currentName

                    );

                }

                $objects[] = new AwareObject(

                    $currentName,
                    Type::getType( $value ),
                    '%' . $name . '%'

                );

            }

        }

        usort( $objects, function ( AwareObject $left, AwareObject $right ): int {

            return strcmp( $left->getName(), $right->getName() );

        } );

        return $objects;

    }

    /**
     * @return AwareObject[]
     */
    public function resolveServices(): array {

        $objects = [];

        foreach ( $this->services as $service ) {

            $definitionType = $service['type'];
            $definitionService = $service['service'] ?? '@' . $definitionType;
            $definitionName = Normalizer::normalizeName( $service['name'] ?? $definitionType );

            $objects[] = new AwareObject(

                $definitionName,
                $definitionType,
                $definitionService

            );

        }

        usort( $objects, function ( AwareObject $left, AwareObject $right ): int {

            return strcmp( $left->getName(), $right->getName() );

        } );

        return $objects;

    }

}
