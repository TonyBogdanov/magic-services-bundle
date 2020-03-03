<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwarePath;

use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Interface ParameterMagicServicesAwarePathAwareInterface
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesAwarePath
 */
interface ParameterMagicServicesAwarePathAwareInterface extends ServiceAwareInterface
{
    /**
     * @return string
     */
    public function getParameterMagicServicesAwarePath(): string;

    /**
     * @required
     *
     * @param string $parameterMagicServicesAwarePath
     *
     * @return $this
     */
    public function setParameterMagicServicesAwarePath(string $parameterMagicServicesAwarePath);
}
