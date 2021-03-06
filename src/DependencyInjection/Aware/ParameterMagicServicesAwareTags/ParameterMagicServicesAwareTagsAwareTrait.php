<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareTags;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Trait ParameterMagicServicesAwareTagsAwareTrait
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareTags
 */
trait ParameterMagicServicesAwareTagsAwareTrait
{
    /** @var array $parameterMagicServicesAwareTags */
    protected $parameterMagicServicesAwareTags;

    /**
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function getParameterMagicServicesAwareTags(): array
    {
        return $this->parameterMagicServicesAwareTags;
    }

    /**
     * @required
     *
     * @param array $parameterMagicServicesAwareTags
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setParameterMagicServicesAwareTags(array $parameterMagicServicesAwareTags)
    {
        $this->parameterMagicServicesAwareTags = $parameterMagicServicesAwareTags;
        return $this;
    }
}
