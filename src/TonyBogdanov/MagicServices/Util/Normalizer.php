<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Util;

/**
 * Class Normalizer
 *
 * @package TonyBogdanov\MagicServices\Util
 */
class Normalizer {

    /**
     * @param string $name
     *
     * @return string
     */
    public static function normalizeName( string $name ): string {

        $parts = explode( '\\', $name );

        if ( 0 < count( $parts ) ) {

            $name = array_pop( $parts );
            $name = preg_replace( '/Interface$/', '', $name );

        }

        $name = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $name ) ) );
        $name = str_replace( ' ', '', ucwords( str_replace( '.', ' ', $name ) ) );

        return $name;

    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function normalizeParameterName( string $name ): string {

        $chars = str_split( $name );

        $index = 0;
        $words = [];

        while ( 0 < count( $chars ) ) {

            $char = array_shift( $chars );
            if ( preg_match( '/^[^a-z0-9]$/', $char ) ) {

                $index++;

            }

            if ( ! isset( $words[ $index ] ) ) {

                $words[ $index ] = '';

            }

            $words[ $index ] .= $char;

        }

        $combined = [];

        while ( 0 < count( $words ) ) {

            $word = array_shift( $words );
            if ( preg_match( '/^[A-Z0-9]$/', $word ) ) {

                if ( 0 < count( $combined ) && preg_match( '/^[A-Z0-9]+$/', $combined[ count( $combined ) - 1 ] ) ) {

                    $combined[ count( $combined ) - 1 ] .= $word;
                    continue;

                }

            }

            $combined[] = $word;

        }

        $combined[0] = strtolower( $combined[0] );
        return implode( '', $combined );

    }

}
