<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareParameters;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Trait ParameterMagicServicesAwareParametersAwareTrait
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareParameters
 */
trait ParameterMagicServicesAwareParametersAwareTrait
{
    /** @var array $parameterMagicServicesAwareParameters */
    protected $parameterMagicServicesAwareParameters;

    /**
     * @return array
     */
    public function getParameterMagicServicesAwareParameters(): array
    {
        return $this->parameterMagicServicesAwareParameters;
    }

    /**
     * @required
     *
     * @param array $parameterMagicServicesAwareParameters
     *
     * @return $this
     */
    public function setParameterMagicServicesAwareParameters(array $parameterMagicServicesAwareParameters)
    {
        $this->parameterMagicServicesAwareParameters = $parameterMagicServicesAwareParameters;
        return $this;
    }
}