<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Util;

/**
 * Class TypeUtil
 *
 * @package TonyBogdanov\MagicServices\Util
 */
class TypeUtil {

    /**
     * @param string $type
     * @param bool $short
     *
     * @return string
     */
    public static function normalize( string $type, bool $short = false ): string {

        if ( $short ) {

            $parts = explode( '\\', $type );
            return $parts[ count( $parts ) - 1 ];

        }

        return $type;

    }

}
