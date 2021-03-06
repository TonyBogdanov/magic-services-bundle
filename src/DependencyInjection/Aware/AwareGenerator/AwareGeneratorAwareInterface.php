<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\AwareGenerator;

use TonyBogdanov\MagicServices\AwareGenerator;
use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Interface AwareGeneratorAwareInterface
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\AwareGenerator
 */
interface AwareGeneratorAwareInterface extends ServiceAwareInterface
{
    /**
     * @return AwareGenerator
     *
     * @codeCoverageIgnore
     */
    public function getAwareGenerator(): AwareGenerator;

    /**
     * @required
     *
     * @param AwareGenerator $awareGenerator
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setAwareGenerator(AwareGenerator $awareGenerator);
}
