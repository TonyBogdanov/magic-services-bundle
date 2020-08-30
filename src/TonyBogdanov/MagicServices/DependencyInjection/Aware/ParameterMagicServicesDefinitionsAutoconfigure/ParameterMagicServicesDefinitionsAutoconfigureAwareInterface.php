<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsAutoconfigure;

use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Interface ParameterMagicServicesDefinitionsAutoconfigureAwareInterface
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsAutoconfigure
 */
interface ParameterMagicServicesDefinitionsAutoconfigureAwareInterface extends ServiceAwareInterface
{
    /**
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function getParameterMagicServicesDefinitionsAutoconfigure(): bool;

    /**
     * @required
     *
     * @param bool $parameterMagicServicesDefinitionsAutoconfigure
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setParameterMagicServicesDefinitionsAutoconfigure(bool $parameterMagicServicesDefinitionsAutoconfigure);
}
