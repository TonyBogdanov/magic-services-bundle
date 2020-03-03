<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterBag;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Interface ParameterBagAwareInterface
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterBag
 */
interface ParameterBagAwareInterface extends ServiceAwareInterface
{
    /**
     * @return ParameterBagInterface
     */
    public function getParameterBag(): ParameterBagInterface;

    /**
     * @required
     *
     * @param ParameterBagInterface $parameterBag
     *
     * @return $this
     */
    public function setParameterBag(ParameterBagInterface $parameterBag);
}
