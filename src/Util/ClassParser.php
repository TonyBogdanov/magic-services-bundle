<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Util;

/**
 * Class ClassParser
 *
 * @package TonyBogdanov\MagicServices\Util
 */
class ClassParser {

    /**
     * @var array[]
     */
    protected $tokens;

    /**
     * @var array[]
     */
    protected $checkpoints = [];

    /**
     * @param array $tokens
     *
     * @return string
     */
    protected function dump( array $tokens ): string {

        return implode( '', array_map( function ( $token ): string {

            return is_array( $token ) ? $token[1] : $token;

        }, $tokens ) );

    }

    /**
     * @return $this
     */
    protected function push() {

        $this->checkpoints[] = $this->tokens;
        return $this;

    }

    /**
     * @return $this
     */
    protected function pop() {

        array_pop( $this->checkpoints );
        return $this;

    }

    /**
     * @return $this
     */
    protected function restore() {

        $this->tokens = array_pop( $this->checkpoints );
        return $this;

    }

    /**
     * @param callable $callback
     *
     * @return mixed
     */
    protected function isolate( callable $callback ) {

        $this->push();

        try {

            $result = call_user_func( $callback );
            $this->pop();

            return $result;

        } catch ( \RuntimeException $e ) {

            $this->restore();
            throw $e;

        }

    }

    /**
     * @return $this
     */
    protected function eof() {

        if ( 0 === count( $this->tokens ) ) {

            throw new \RuntimeException();

        }

        return $this;

    }

    /**
     * @param int $type
     *
     * @return array
     */
    protected function parseToken( int $type): array {

        return $this->eof()->isolate( function () use ( $type ): array {

            $token = array_shift( $this->tokens );
            if ( ! is_array( $token ) || $type !== $token[0] ) {

                throw new \RuntimeException();

            }

            return $token;

        } );

    }

    /**
     * @param int $type
     *
     * @return array
     */
    protected function parseUntil( int $type ): array {

        return $this->eof()->isolate( function () use ( $type ): array {

            while ( true ) {

                $this->eof();

                try {

                    return $this->parseToken( $type );

                } catch ( \RuntimeException $e ) {

                    array_shift( $this->tokens );

                }

            }

            throw new \RuntimeException();

        } );

    }

    /**
     * @param array $types
     *
     * @return array
     */
    protected function parseSequence( array $types ): array {

        return $this->eof()->isolate( function () use ( $types ): array {

            return array_map( function ( int $type ): array {

                return $this->parseToken( $type );

            }, $types );

        } );

    }

    /**
     * @return string
     */
    protected function parseNamespace(): string {

        return $this->eof()->isolate( function () {

            $this->parseUntil( T_NAMESPACE );
            $this->parseToken( T_WHITESPACE );

            $namespace = [ $this->parseToken( T_STRING ) ];

            while ( true ) {

                try {

                    $namespace = array_merge( $namespace, $this->parseSequence( [ T_NS_SEPARATOR, T_STRING ] ) );

                } catch ( \RuntimeException $e ) {

                    break;

                }

            }

            return $this->dump( $namespace );

        } );

    }

    /**
     * @return string
     */
    protected function parseClass(): string {

        return $this->eof()->isolate( function () {

            $this->parseUntil( T_CLASS );
            $this->parseToken( T_WHITESPACE );

            return $this->dump( [ $this->parseToken( T_STRING ) ] );

        } );

    }

    /**
     * ClassParser constructor.
     *
     * @param array $tokens
     */
    public function __construct( array $tokens ) {

        $this->tokens = $tokens;

    }

    /**
     * @return string|null
     */
    public function parse(): ?string {

        try {

            $namespace = $this->parseNamespace();

        } catch ( \RuntimeException $e ) {}

        try {

            $class = $this->parseClass();

        } catch ( \RuntimeException $e ) {

            return null;

        }

        return ( isset( $namespace ) ? $namespace . '\\' : '' ) . $class;

    }

}
