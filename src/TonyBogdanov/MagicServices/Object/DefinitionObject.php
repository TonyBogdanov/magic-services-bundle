<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Object;

use TonyBogdanov\MagicServices\Annotation\MagicService;
use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;
use TonyBogdanov\Memoize\Traits\MemoizeTrait;

/**
 * Class DefinitionObject
 *
 * @package TonyBogdanov\MagicServices\Object
 */
class DefinitionObject {

    use MemoizeTrait;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var MagicService|null
     */
    protected $annotation;

    /**
     * DefinitionObject constructor.
     *
     * @param \ReflectionClass $reflection
     * @param MagicService|null $annotation
     */
    public function __construct( \ReflectionClass $reflection, ?MagicService $annotation ) {

        $this
            ->setReflection( $reflection )
            ->setAnnotation( $annotation );

    }

    /**
     * @return bool
     */
    public function supportsSetters(): bool {

        return ! $this->getAnnotation() || $this->getAnnotation()->isSetters();

    }

    /**
     * @return string
     */
    public function getName(): string {

        return $this->getReflection()->getName();

    }

    /**
     * @return bool
     */
    public function isIgnored(): bool {

        return $this->getAnnotation() && $this->getAnnotation()->isIgnore();

    }

    /**
     * @return array
     */
    public function getTags(): array {

        return $this->getAnnotation() ? $this->getAnnotation()->getTags() : [];

    }

    /**
     * @return bool
     */
    public function isPublic(): bool {

        return $this->getAnnotation() && $this->getAnnotation()->isPublic();

    }

    /**
     * @return string[]
     */
    public function getAwareInterfaceNames(): array {

        return $this->memoize( __METHOD__, function (): array {

            $result = [];

            /** @var \ReflectionClass $interface */
            foreach ( $this->getReflection()->getInterfaces() as $interface ) {

                if (

                    $interface->getName() === ServiceAwareInterface::class ||
                    ! $interface->implementsInterface( ServiceAwareInterface::class )

                ) {

                    continue;

                }

                $result[] = $interface->getName();

            }

            return $result;

        } );

    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getConstructorDependencies(): array {

        if ( ! $this->getReflection()->hasMethod( '__construct' ) ) {

            return [];

        }

        return array_map( function ( \ReflectionParameter $parameter ): array {

            /** @var \ReflectionNamedType $type */
            $type = $parameter->getType();

            return [

                'name' => $parameter->getName(),
                'type' => $type->getName(),

            ];

        }, $this->getReflection()->getMethod( '__construct' )->getParameters() );

    }

    /**
     * @return \ReflectionClass
     */
    public function getReflection(): \ReflectionClass {

        return $this->reflection;

    }

    /**
     * @param \ReflectionClass $reflection
     *
     * @return DefinitionObject
     */
    public function setReflection( \ReflectionClass $reflection ): DefinitionObject {

        $this->reflection = $reflection;
        return $this;

    }

    /**
     * @return MagicService|null
     */
    public function getAnnotation(): ?MagicService {

        return $this->annotation;

    }

    /**
     * @param MagicService|null $annotation
     *
     * @return DefinitionObject
     */
    public function setAnnotation( MagicService $annotation = null ): DefinitionObject {

        $this->annotation = $annotation;
        return $this;

    }

}
