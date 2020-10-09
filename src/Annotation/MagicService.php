<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Annotation;

/**
 * Class MagicService
 *
 * @package TonyBogdanov\MagicServices\Annotation
 *
 * @Annotation
 */
class MagicService {

    const UNDEFINED = '__UNDEFINED__';
    const DEFAULTS = [

        'ignore' => false,
        'setters' => true,
        'tags' => [],
        'public' => false,

    ];

    /**
     * Set this to TRUE to define that the inspected service must be ignored by the magic services definition generator.
     *
     * This could be useful when you need to write the definition yourself, but the class still implements the
     * ServiceAwareInterface (or uses aware interfaces) and thus is considered for generation.
     *
     * @var bool
     */
    public $ignore = self::UNDEFINED;

    /**
     * Set this to FALSE to define that the inspected service does not support magic setters and must have all of its
     * dependencies injected as constructor arguments instead.
     *
     * @var bool
     */
    public $setters = self::UNDEFINED;

    /**
     * Define a list of service tags.
     *
     * @var array
     */
    public $tags = self::UNDEFINED;

    /**
     * Set this to TRUE to define the service as public.
     *
     * @var bool
     */
    public $public = self::UNDEFINED;

    /**
     * @param string $key
     *
     * @return bool
     */
    protected function isset( string $key ): bool {

        return static::UNDEFINED !== $this->$key;

    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function get( string $key ) {

        if ( $this->isset( $key ) ) {

            return $this->$key;

        }

        return static::DEFAULTS[ $key ];

    }

    /**
     * @return bool
     */
    public function isIgnore(): bool {

        return $this->get( 'ignore' );

    }

    /**
     * @return bool
     */
    public function isSetters(): bool {

        return $this->get( 'setters' );

    }

    /**
     * @return array
     */
    public function getTags(): array {

        return $this->get( 'tags' );

    }

    /**
     * @return bool
     */
    public function isPublic(): bool {

        return $this->get( 'public' );

    }

    /**
     * @param MagicService $annotation
     *
     * @return $this
     */
    public function merge( MagicService $annotation ): self {

        foreach ( static::DEFAULTS as $key => $default ) {

            if ( ! $this->isset( $key ) && $annotation->isset( $key ) ) {

                $this->$key = $annotation->get( $key );

            }

        }

        return $this;

    }

}
