<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsPath;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Trait ParameterMagicServicesDefinitionsPathAwareTrait
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsPath
 */
trait ParameterMagicServicesDefinitionsPathAwareTrait
{
    /** @var string $parameterMagicServicesDefinitionsPath */
    protected $parameterMagicServicesDefinitionsPath;

    /**
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getParameterMagicServicesDefinitionsPath(): string
    {
        return $this->parameterMagicServicesDefinitionsPath;
    }

    /**
     * @required
     *
     * @param string $parameterMagicServicesDefinitionsPath
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setParameterMagicServicesDefinitionsPath(string $parameterMagicServicesDefinitionsPath)
    {
        $this->parameterMagicServicesDefinitionsPath = $parameterMagicServicesDefinitionsPath;
        return $this;
    }
}
