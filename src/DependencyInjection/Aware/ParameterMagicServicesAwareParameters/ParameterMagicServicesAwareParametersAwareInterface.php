<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareParameters;

use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Interface ParameterMagicServicesAwareParametersAwareInterface
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwareParameters
 */
interface ParameterMagicServicesAwareParametersAwareInterface extends ServiceAwareInterface
{
    /**
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function getParameterMagicServicesAwareParameters(): array;

    /**
     * @required
     *
     * @param array $parameterMagicServicesAwareParameters
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setParameterMagicServicesAwareParameters(array $parameterMagicServicesAwareParameters);
}
