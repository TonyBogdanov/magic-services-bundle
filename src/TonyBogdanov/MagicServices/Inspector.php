<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices;

use Nette\PhpGenerator\Type;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Tag\TaggedValue;
use TonyBogdanov\MagicServices\Annotation\MagicService;
use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\AnnotationReader\AnnotationReaderAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\AnnotationReader\AnnotationReaderAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterBag\ParameterBagAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterBag\ParameterBagAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareParameters\ParameterMagicServicesAwareParametersAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareParameters\ParameterMagicServicesAwareParametersAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareServices\ParameterMagicServicesAwareServicesAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareServices\ParameterMagicServicesAwareServicesAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareTags\ParameterMagicServicesAwareTagsAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareTags\ParameterMagicServicesAwareTagsAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsServices\ParameterMagicServicesDefinitionsServicesAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsServices\ParameterMagicServicesDefinitionsServicesAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\TaggedMagicServicesEvent_Subscriber\TaggedMagicServicesEvent_SubscriberAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\TaggedMagicServicesEvent_Subscriber\TaggedMagicServicesEvent_SubscriberAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Singleton\ContainerBuilderSingleton;
use TonyBogdanov\MagicServices\Object\AwareObject;
use TonyBogdanov\MagicServices\Object\DefinitionObject;
use TonyBogdanov\MagicServices\Util\ClassFinder;
use TonyBogdanov\MagicServices\Util\Normalizer;

/**
 * Class Inspector
 *
 * @package TonyBogdanov\MagicServices
 *
 * @MagicService()
 */
class Inspector implements
    AnnotationReaderAwareInterface,
    ParameterBagAwareInterface,
    ParameterMagicServicesAwareParametersAwareInterface,
    ParameterMagicServicesAwareTagsAwareInterface,
    ParameterMagicServicesAwareServicesAwareInterface,
    ParameterMagicServicesDefinitionsServicesAwareInterface
{

    use AnnotationReaderAwareTrait;
    use ParameterBagAwareTrait;
    use ParameterMagicServicesAwareParametersAwareTrait;
    use ParameterMagicServicesAwareTagsAwareTrait;
    use ParameterMagicServicesAwareServicesAwareTrait;
    use ParameterMagicServicesDefinitionsServicesAwareTrait;

    /**
     * @param ReflectionClass $reflection
     *
     * @return MagicService|null
     */
    protected function resolveAnnotation( ReflectionClass $reflection ): ?MagicService {

        $parent = $reflection->getParentClass() ? $this->resolveAnnotation( $reflection->getParentClass() ) : null;

        /** @var MagicService $annotation */
        $annotation = $this->getAnnotationReader()->getClassAnnotation( $reflection, MagicService::class );

        return $parent ? ( $annotation ? $annotation->merge( $parent ) : $parent ) : $annotation;

    }

    /**
     * @return AwareObject[]
     */
    public function resolveAwareParameters(): array {

        $objects = [];

        foreach ( $this->getParameterMagicServicesAwareParameters() as $parameter ) {

            $matchRegex = $parameter['regex'];
            $matchName = $parameter['name'] ?? 'Parameter$0';

            foreach ( $this->getParameterBag()->all() as $name => $value ) {

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

                $objects[] = new AwareObject( $currentName, Type::getType( $value ), '%' . $name . '%' );

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
    public function resolveAwareTags(): array {

        $objects = [];

        foreach ( $this->getParameterMagicServicesAwareTags() as $tag ) {

            $matchRegex = $tag['regex'];
            $matchName = $tag['name'] ?? 'Tagged$0';

            foreach ( ContainerBuilderSingleton::getContainerBuilder()->findTags() as $value ) {

                if ( ! preg_match( $matchRegex, $value, $matches ) ) {

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

                $objects[] = new AwareObject( $currentName, 'iterable', new TaggedValue( 'tagged', $value ) );

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

        foreach ( $this->getParameterMagicServicesAwareServices() as $service ) {

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
     * @throws ReflectionException
     */
    public function resolveDefinitions( bool $includeIgnores = true ): array {

        $definitions = [];

        foreach ( ClassFinder::findClasses( $this->parameterMagicServicesDefinitionsServices ) as $class ) {

            $reflection = new ReflectionClass( $class );
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
