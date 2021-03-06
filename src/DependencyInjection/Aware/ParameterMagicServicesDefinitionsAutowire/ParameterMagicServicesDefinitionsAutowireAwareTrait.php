<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsAutowire;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Trait ParameterMagicServicesDefinitionsAutowireAwareTrait
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsAutowire
 */
trait ParameterMagicServicesDefinitionsAutowireAwareTrait
{
    /** @var bool $parameterMagicServicesDefinitionsAutowire */
    protected $parameterMagicServicesDefinitionsAutowire;

    /**
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function getParameterMagicServicesDefinitionsAutowire(): bool
    {
        return $this->parameterMagicServicesDefinitionsAutowire;
    }

    /**
     * @required
     *
     * @param bool $parameterMagicServicesDefinitionsAutowire
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setParameterMagicServicesDefinitionsAutowire(bool $parameterMagicServicesDefinitionsAutowire)
    {
        $this->parameterMagicServicesDefinitionsAutowire = $parameterMagicServicesDefinitionsAutowire;
        return $this;
    }
}
