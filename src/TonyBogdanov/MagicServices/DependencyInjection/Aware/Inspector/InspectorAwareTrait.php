<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector;

use TonyBogdanov\MagicServices\Inspector;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Trait InspectorAwareTrait
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector
 */
trait InspectorAwareTrait
{
    /** @var Inspector $inspector */
    protected $inspector;

    /**
     * @return Inspector
     */
    public function getInspector(): Inspector
    {
        return $this->inspector;
    }

    /**
     * @required
     *
     * @param Inspector $inspector
     *
     * @return $this
     */
    public function setInspector(Inspector $inspector)
    {
        $this->inspector = $inspector;
        return $this;
    }
}