<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareTags;

use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Interface ParameterMagicServicesAwareTagsAwareInterface
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareTags
 */
interface ParameterMagicServicesAwareTagsAwareInterface extends ServiceAwareInterface
{
    /**
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function getParameterMagicServicesAwareTags(): array;

    /**
     * @required
     *
     * @param array $parameterMagicServicesAwareTags
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setParameterMagicServicesAwareTags(array $parameterMagicServicesAwareTags);
}
