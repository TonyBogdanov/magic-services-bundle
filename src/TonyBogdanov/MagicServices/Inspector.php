<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices;

use Doctrine\Common\Annotations\Reader;
use Nette\PhpGenerator\Type;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TonyBogdanov\MagicServices\Annotation\MagicService;
use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;
use TonyBogdanov\MagicServices\Object\AwareObject;
use TonyBogdanov\MagicServices\Object\DefinitionObject;
use TonyBogdanov\MagicServices\Util\ClassFinder;
use TonyBogdanov\MagicServices\Util\Normalizer;

/**
 * Class Inspector
 *
 * @package TonyBogdanov\MagicServices
 */
class Inspector {

    /**
     * @var Reader
     */
    protected $annotationReader;

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var string[]
     */
    protected $awareParameters;

    /**
     * @var string[]
     */
    protected $awareServices;

    /**
     * @var string[]
     */
    protected $definitions;

    /**
     * @param \ReflectionClass $reflection
     *
     * @return MagicService|null
     */
    protected function resolveAnnotation( \ReflectionClass $reflection ): ?MagicService {

        $parent = $reflection->getParentClass() ? $this->resolveAnnotation( $reflection->getParentClass() ) : null;

        /** @var MagicService $annotation */
        $annotation = $this->annotationReader->getClassAnnotation( $reflection, MagicService::class );

        return $parent ? ( $annotation ? $annotation->merge( $parent ) : $parent ) : $annotation;

    }

    /**
     * Inspector constructor.
     *
     * @param Reader $annotationReader
     * @param ParameterBagInterface $parameterBag
     * @param array $awareParameters
     * @param array $awareServices
     * @param array $definitions
     */
    public function __construct(

        Reader $annotationReader,
        ParameterBagInterface $parameterBag,
        array $awareParameters,
        array $awareServices,
        array $definitions

    ) {

        $this->annotationReader = $annotationReader;
        $this->parameterBag = $parameterBag;

        $this->awareParameters = $awareParameters;
        $this->awareServices = $awareServices;

        $this->definitions = $definitions;

    }

    /**
     * @return AwareObject[]
     */
    public function resolveAwareParameters(): array {

        $objects = [];

        foreach ( $this->awareParameters as $parameter ) {

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
    public function resolveAwareServices(): array {

        $objects = [];

        foreach ( $this->awareServices as $service ) {

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

    /**
     * @param bool $includeIgnores
     *
     * @return DefinitionObject[]
     * @throws \ReflectionException
     */
    public function resolveDefinitions( bool $includeIgnores = true ): array {

        $definitions = [];

        foreach ( ClassFinder::findClasses( $this->definitions ) as $class ) {

            $reflection = new \ReflectionClass( $class );
            if ( $reflection->isAbstract() ) {

                continue;

            }

            /** @var MagicService|null $annotation */
            $annotation = $this->resolveAnnotation( $reflection );
            if ( ! $reflection->implementsInterface( ServiceAwareInterface::class ) && ! $annotation ) {

                continue;

            }

            if ( ! $includeIgnores && $annotation && $annotation->isIgnore() ) {

                continue;

            }

            $definitions[] = new DefinitionObject( $reflection, $annotation );

        }

        return $definitions;

    }

}
