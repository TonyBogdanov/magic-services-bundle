<?php

namespace TonyBogdanov\MagicServices\DependencyInjection\Aware\CodeGenerator;

use TonyBogdanov\MagicServices\Aware\ServiceAwareInterface;
use TonyBogdanov\MagicServices\CodeGenerator;

/**
 * This file was automatically generated by the tonybogdanov/magic-services-bundle package.
 * Do not manually modify this file.
 *
 * Interface CodeGeneratorAwareInterface
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection\Aware\CodeGenerator
 */
interface CodeGeneratorAwareInterface extends ServiceAwareInterface
{
    /**
     * @return CodeGenerator
     *
     * @codeCoverageIgnore
     */
    public function getCodeGenerator(): CodeGenerator;

    /**
     * @required
     *
     * @param CodeGenerator $codeGenerator
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setCodeGenerator(CodeGenerator $codeGenerator);
}
