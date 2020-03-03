<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\AwareGenerator;

use TonyBogdanov\MagicServices\AwareGenerator;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Trait AwareGeneratorAwareTrait
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\AwareGenerator
 */
trait AwareGeneratorAwareTrait
{
    /** @var AwareGenerator $awareGenerator */
    protected $awareGenerator;

    /**
     * @return AwareGenerator
     */
    public function getAwareGenerator(): AwareGenerator
    {
        return $this->awareGenerator;
    }

    /**
     * @required
     *
     * @param AwareGenerator $awareGenerator
     *
     * @return $this
     */
    public function setAwareGenerator(AwareGenerator $awareGenerator)
    {
        $this->awareGenerator = $awareGenerator;
        return $this;
    }
}
