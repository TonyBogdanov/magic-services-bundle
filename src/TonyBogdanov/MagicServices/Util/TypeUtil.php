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
     * @see https://github.com/nette/php-generator/issues/57
     *
     * @param string $type
     * @param bool $short
     *
     * @return string
     */
    public static function normalize( string $type, bool $short = false ): string {

        switch ( $type ) {

            case 'integer':
                return 'int';

            case 'boolean':
                return 'bool';

            default:
                if ( $short ) {

                    $parts = explode( '\\', $type );
                    return $parts[ count( $parts ) - 1 ];

                }

                return $type;

        }

    }

}
