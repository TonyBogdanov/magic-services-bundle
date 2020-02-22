<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Services;

use Symfony\Component\Console\Command\Command;

/**
 * Class Inspect
 *
 * @package TonyBogdanov\MagicServices\Command\Services
 */
class Inspect extends Command {

    /**
     * Inspect constructor.
     */
    public function __construct() {

        parent::__construct( 'services:inspect' );

    }

}
