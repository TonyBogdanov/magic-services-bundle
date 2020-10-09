<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Util;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ClassFinder
 *
 * @package TonyBogdanov\MagicServices\Util
 */
class ClassFinder {

    /**
     * @param string[] $paths
     *
     * @return string[]
     */
    public static function findClasses( array $paths ): array {

        $classes = [];

        /** @var SplFileInfo $file */
        foreach ( ( new Finder() )->in( $paths )->name( '*.php' )->files() as $file ) {

            $tokens = token_get_all( $file->getContents() );
            $parser = new ClassParser( $tokens );

            $class = $parser->parse();
            if ( $class ) {

                $classes[] = $class;

            }

        }

        return $classes;

    }

}
