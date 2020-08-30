<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\AnnotationReader;

use Doctrine\Common\Annotations\Reader;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Trait AnnotationReaderAwareTrait
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\AnnotationReader
 */
trait AnnotationReaderAwareTrait
{
    /** @var Reader $annotationReader */
    protected $annotationReader;

    /**
     * @return Reader
     *
     * @codeCoverageIgnore
     */
    public function getAnnotationReader(): Reader
    {
        return $this->annotationReader;
    }

    /**
     * @required
     *
     * @param Reader $annotationReader
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setAnnotationReader(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
        return $this;
    }
}
