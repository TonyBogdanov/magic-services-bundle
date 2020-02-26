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

    /**
     * Set this to TRUE to define the service as public.
     *
     * @var bool
     */
    public $public = false;

    /**
     * Define a list of service tags.
     *
     * @var array
     */
    public $tags = [];

}
