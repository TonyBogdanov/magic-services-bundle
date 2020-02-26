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

}
