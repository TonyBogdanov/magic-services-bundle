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
     * Set this to TRUE to define that the inspected service must be ignored by the magic services definition generator.
     *
     * This could be useful when you need to write the definition yourself, but the class still implements the
     * ServiceAwareInterface (or uses aware interfaces) and thus is considered for generation.
     *
     * @var bool
     */
    public $ignore = false;

    /**
     * Set this to FALSE to define that the inspected service does not support magic setters and must have all of its
     * dependencies injected as constructor arguments instead.
     *
     * @var bool
     */
    public $setters = true;

    /**
     * Define a list of service tags.
     *
     * @var array
     */
    public $tags = [];

    /**
     * Set this to TRUE to define the service as public.
     *
     * @var bool
     */
    public $public = false;

}
